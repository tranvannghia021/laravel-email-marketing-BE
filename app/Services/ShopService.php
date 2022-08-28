<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShopService
{
    
    /**
     * isCheckShop
     *
     * @param  mixed $name
     * @return status
     */
    public function isCheckShop($name)
    {
        $response = Http::get('https://' . $name . '.myshopify.com/admin/api/' . env('VERTION_API_SHOPIFY') . '/shop.json');

        return $response->status();
    }    
    /**
     * getAccessToken
     *
     * @param  mixed $domain
     * @param  mixed $code
     * @return token
     */
    public function getAccessToken($domain, $code)
    {
        $response = Http::post('https://' . $domain . '/admin/oauth/access_token', [
            'client_id' => env('API_KEY_SHOPIFY_APP'),
            'client_secret' => env('API_SECRET_KEY_SHOPIFY_APP'),
            'code' => $code
        ]);
        return $response->json('access_token');
    }    
    /**
     * getInfoShop
     *
     * @param  mixed $accessToken
     * @param  mixed $domain
     * @return info
     */
    public function getInfoShop($accessToken, $domain)
    {
        return Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->get('https://' . $domain . '/admin/api/' . env('VERTION_API_SHOPIFY') . '/shop.json');
    }
}
