<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeHookService
{


    /**
     * register
     *
     * @param  mixed $shops
     * @return void
     */
    public function register($shops)
    {
        $arrayCreate = explode(',', env('TOPIC_SHOPIFY_CREATE'));
        $arrayUpdate = explode(',', env('TOPIC_SHOPIFY_UPDATE'));
        $arrayDelete = explode(',', env('TOPIC_SHOPIFY_DELETE'));
        $arrayUnInstall = explode(',', env('TOPIC_SHOPIFY_UNINSTALL'));

        foreach ($arrayCreate as $topic) {

            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/' . env('VERTION_API_SHOPIFY') . '/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => '' . env('APP_URL') . '/api/webhook/customer/create',
                    'format' => 'json'

                ]
            ]);
        }
        foreach ($arrayUpdate as $topic) {
            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/' . env('VERTION_API_SHOPIFY') . '/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => '' . env('APP_URL') . '/api/webhook/customer/update',
                    'format' => 'json'

                ]
            ]);
        }
        foreach ($arrayDelete as $topic) {
            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/' . env('VERTION_API_SHOPIFY') . '/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => '' . env('APP_URL') . '/api/webhook/customer/delete',
                    'format' => 'json'

                ]
            ]);
        }

        // uninstall app
        foreach ($arrayUnInstall as $topic) {
            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/' . env('VERTION_API_SHOPIFY') . '/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => '' . env('APP_URL') . '/api/webhook/store/uninstall',
                    'format' => 'json'

                ]
            ]);
        }
    }
}
