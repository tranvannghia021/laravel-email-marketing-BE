<?php

namespace App\Http\Controllers\api\auth;

use App\Events\LoginEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Login\LoginRequest;
use App\Http\Requests\Login\VerifyRequest;
use App\Http\Resources\Shop\ShopResource;
use App\Repositories\ShopRepository;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    protected $shopService;
    protected $shopRepos;
    public function __construct(ShopService $shopService, ShopRepository $shopRepos)
    {
        $this->shopService = $shopService;
        $this->shopRepos = $shopRepos;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {

        $name = trim($request->domain);
        $response = $this->shopService->isCheckShop($name);
        // thông báo khi không có
        if ($response == Response::HTTP_NOT_FOUND) {

            return response()->json([
                'success' => false,
                'message' => 'Account not found!'
            ], Response::HTTP_NOT_FOUND);
        } else {
            // Lấy hmac và code
            return response()->json([
                'success' => true,
                'message' => 'Link redirect',
                'link' => 'https://' . $name .
                    '.myshopify.com/admin/oauth/authorize?client_id=' .
                    env('API_KEY_SHOPIFY_APP') .
                    '&scope=' . env('SCOPE_SHOPIFY') .
                    '&redirect_uri=' . env('REDIRECT_URL')
            ]);
        }
    }




    /**
     * loginShopify
     *
     * @param  mixed $request
     * @return response
     */
    public function loginShopify(VerifyRequest $request)
    {
        $domain = $request->shop;
        $code = $request->code;
        $isHmac = $this->verifyHmac($request);
        if ($isHmac) {

            $dataShops = $this->shopRepos->getByDomain($domain);
            if (is_null($dataShops)) {
                // Lấy access_token
                $accessToken = $this->shopService->getAccessToken($domain, $code);
                $responseShop = $this->shopService->getInfoShop($accessToken, $domain);
                $infoShop = $responseShop->json('shop');

                $result = $this->shopRepos->create(
                    [
                        'id' => $infoShop['id'],
                        'name' => $infoShop['name'],
                        'email' => $infoShop['email'],
                        'shopify_domain' => $infoShop['domain'],
                        'hash_domain' => Hash::make($infoShop['domain']),
                        'access_token' => $accessToken
                    ]
                );

                return $this->eventListenCreateToken($result);
            } else {
                if ($dataShops->status == 'uninstall') {

                    $accessToken = $this->shopService->getAccessToken($domain, $code);

                    $responseShop = $this->shopService->getInfoShop($accessToken, $domain);
                    $infoShop = $responseShop->json('shop');
                    $resultUpdated = $this->shopRepos->update($dataShops->id, [
                        'name' => $infoShop['name'],
                        'email' => $infoShop['email'],
                        'shopify_domain' => $infoShop['domain'],
                        'status' => 'install',
                        'hash_domain' => Hash::make($infoShop['domain']),
                        'access_token' => $accessToken
                    ]);
                    if ($resultUpdated) {
                        $dataShops = $this->shopRepos->find($dataShops->id);
                    }
                    return $this->eventListenCreateToken($dataShops);
                }

                $token = $this->createToken($dataShops);

                return $this->responseLogin($token, true);
            }
        } else {

            return $this->responseLoginFailded();
        }
    }


    /**
     * verifyHmac
     *
     * @param  mixed $request
     * @return bool
     */
    protected function verifyHmac($request)
    {
        $query = http_build_query([
            'code' => is_null($request->code) ? null : $request->code,
            'host' => is_null($request->host) ? null : $request->host,
            'shop' => is_null($request->shop) ? null : $request->shop,
            'timestamp' => is_null($request->timestamp) ? null : $request->timestamp
        ]);

        $hmacShopify = is_null($request->hmac) ? null : $request->hmac;

        $hmacApp = hash_hmac('sha256', $query, env('API_SECRET_KEY_SHOPIFY_APP'));

        if ($hmacApp === $hmacShopify) {
            return true;
        }
        return false;
    }


    /**
     * responseLogin
     *
     * @param  mixed $dataShops
     * @param  mixed $token
     * @param  mixed $loaded
     * @return response
     */
    public function responseLogin($token, $loaded = false)
    {
        if (is_null($token)) {

            return $this->responseLoginFailded();
        }
        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expried_in' => auth()->factory()->getTTL() * 60,
            'loaded' => $loaded,
            'data' => []
        ]);
    }


    /**
     * refresh
     *
     * @param  mixed $request
     * @return response
     */
    public function refresh(Request $request)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                $newToken = JWTAuth::parseToken()->refresh(true);

                return $this->responseLogin($newToken, true);
            }
        }
    }


    /**
     * me
     *
     * @return response
     */
    public function me()
    {
        $shops = auth()->user();

        return response()->json([
            'success' => true,
            'message' => 'Info shops',
            'data' => new ShopResource($shops),
        ]);
    }

    /**
     * logout
     *
     * @return response
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout successfully'
        ]);
    }


    /**
     * createToken
     *
     * @param  mixed $shops
     * @return token
     */
    protected function createToken($shops)
    {
        return auth()->attempt([
            'id' => $shops->id,
            'name' => $shops->name,
            'shopify_domain' => $shops->shopify_domain,
            'password' => $shops->shopify_domain
        ]);
    }


    /**
     * eventListenCreateToken
     *
     * @param  mixed $dataShop
     * @return void
     */
    protected function eventListenCreateToken($dataShop)
    {
        if (!is_null($dataShop)) {
            event(new LoginEvent($dataShop));
            $token = $this->createToken($dataShop);

            return $this->responseLogin($token, false);
        } else {

            return $this->responseLoginFailded();
        }
    }


    /**
     * responseLoginFailded
     *
     * @return void
     */
    protected function responseLoginFailded()
    {
        return response()->json([
            'success' => false,
            'message' => 'Login failed, there was an error in the login session.',
            'data' => []
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
