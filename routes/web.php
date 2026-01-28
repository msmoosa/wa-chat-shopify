<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExternalStoreController;
use App\Http\Controllers\ShopConfigController;
use App\Http\Controllers\TestPageController;
use App\Http\Controllers\CheckoutsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['verify.shopify'])->name('home');

// Test page route
Route::get('/test/page', [TestPageController::class, 'index'])
    ->name('test.page');

// API endpoint to get authenticated shop's config
Route::get('/api/shop-config', [ShopConfigController::class, 'getConfig'])
    ->middleware(['verify.shopify'])
    ->name('api.shop-config');

// API endpoint to save authenticated shop's config
Route::post('/api/shop-config', [ShopConfigController::class, 'saveConfig'])
    ->middleware(['verify.shopify'])
    ->name('api.shop-config.save');

// API endpoint to get abandoned checkouts for the authenticated shop
Route::get('/api/abandonedCheckouts', [CheckoutsController::class, 'getAbandonedCheckouts'])
    ->middleware(['verify.shopify'])
    ->name('api.abandoned-checkouts');

// API endpoint to mark a checkout as message sent
Route::post('/api/checkouts/{checkoutId}/send-message', [CheckoutsController::class, 'sendMessage'])
    ->middleware(['verify.shopify'])
    ->name('api.checkouts.send-message');

// External API endpoint to get store/user by domain
Route::match(['GET', 'OPTIONS'], '/external/store', [ExternalStoreController::class, 'getStore'])
    ->name('external.store');
