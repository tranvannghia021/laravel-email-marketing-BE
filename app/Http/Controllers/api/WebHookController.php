<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\CreateWebHookJob;
use App\Jobs\DeleteWebHookJob;
use App\Jobs\UnInstallWebHookJob;
use App\Jobs\UpdateWebHookJob;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebHookController extends Controller
{
    protected $shopRepo;
    public function __construct(ShopRepository $shopRepo, Request $request)
    {
        $this->shopRepo = $shopRepo->getByDomain($request->header('X-Shopify-Shop-Domain'));
    }


    /**
     * createWebHook
     *
     * @param  mixed $request
     * @return response status 200
     */
    public function createWebHook(Request $request)
    {
        if (!is_null($this->shopRepo)) {

            dispatch(new CreateWebHookJob($request->toArray(), $this->shopRepo));
        }

        return $this->response();
    }


    /**
     * updateWebHook
     *
     * @param  mixed $request
     * @return response status 200
     */
    public function updateWebHook(Request $request)
    {
        if (!is_null($this->shopRepo)) {

            dispatch(new UpdateWebHookJob($request->toArray(), $this->shopRepo));
        }
        return $this->response();
    }


    /**
     * deleteWebHook
     *
     * @param  mixed $request
     * @return response status 200
     */
    public function deleteWebHook(Request $request)
    {
        if (!is_null($this->shopRepo)) {

            dispatch(new DeleteWebHookJob($request->toArray(), $this->shopRepo));
        }
        return $this->response();
    }



    public function uninstallWebHook(Request $request)
    {

        if (!is_null($this->shopRepo)) {

            dispatch(new UnInstallWebHookJob($this->shopRepo));
        }
        return $this->response();
    }


    public function response()
    {
        return response()->json([]);
    }
}
