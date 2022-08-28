<?php


use App\Http\Controllers\api\auth\LoginController;
use App\Http\Controllers\api\CampaignController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\ExportController;
use App\Http\Controllers\api\WebHookController;

use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::get('/login', [LoginController::class, 'store']);
    Route::post('/login/verify', [LoginController::class, 'loginShopify']);
    Route::post('/refresh', [LoginController::class, 'refresh']);
});

Route::middleware(['authTokenApi'])->prefix('/')->group(function () {
    // api cus
    Route::get('/customer', [CustomerController::class, 'getCustomer']);
    // api campaign
    Route::prefix('/campaign')->group(function () {
        Route::get('/list', [CampaignController::class, 'getAllCampaign']);
        Route::post('/experiment', [CampaignController::class, 'sendTestMail']);
        Route::post('/create', [CampaignController::class, 'store']);
    });


    // logout token
    Route::post('auth/logout', [LoginController::class, 'logout']);
    // info shop
    Route::post('auth/shop-info', [LoginController::class, 'me']);
    // export csv
    Route::post('/export-csv', [ExportController::class, 'exportCSV']);
    // mannal sync
    Route::get('/mannal-sync', [ExportController::class, 'manualSync']);
});
// Nhận sự kiện webhook 
Route::middleware(['authHmac'])->prefix('webhook/customer')->group(function () {
    Route::post('create', [WebHookController::class, 'createWebHook']);
    Route::post('update', [WebHookController::class, 'updateWebHook']);
    Route::post('delete', [WebHookController::class, 'deleteWebHook']);
});
Route::middleware(['authHmac'])->prefix('webhook/store')->group(function () {
    Route::post('uninstall', [WebHookController::class, 'uninstallWebHook']);
});
Route::options('{any}', ['middleware' => ['CorsApi'], function () {
    return response([
        'success' => true
    ]);
}])->where('any', '.*');
