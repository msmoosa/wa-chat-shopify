<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExternalStoreController;
use App\Http\Controllers\ShopConfigController;
use App\Http\Controllers\TestPageController;
use App\Http\Controllers\CheckoutsController;
use App\Http\Controllers\ManualTemplateController;
use App\Http\Controllers\AutomationsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['verify.shopify'])
    ->name('home');

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

// API endpoint to get recoveries (completed checkouts with message sent)
Route::get('/api/recoveries', [CheckoutsController::class, 'getRecoveries'])
    ->middleware(['verify.shopify'])
    ->name('api.recoveries');

// Manual template endpoints
Route::get('/manualtemplates', [ManualTemplateController::class, 'index'])
    ->middleware(['verify.shopify'])
    ->name('manualtemplates.index');

Route::post('/manualtemplates/save', [ManualTemplateController::class, 'save'])
    ->middleware(['verify.shopify'])
    ->name('manualtemplates.save');

Route::post('/manualtemplates/delete', [ManualTemplateController::class, 'delete'])
    ->middleware(['verify.shopify'])
    ->name('manualtemplates.delete');

// API endpoint to mark a checkout as message sent
Route::post('/api/checkouts/{checkoutId}/send-message', [CheckoutsController::class, 'sendMessage'])
    ->middleware(['verify.shopify'])
    ->name('api.checkouts.send-message');

// API endpoint to get checkout analytics for the authenticated shop
Route::get('/api/checkouts/analytics', [CheckoutsController::class, 'getAnalytics'])
    ->middleware(['verify.shopify'])
    ->name('api.checkouts.analytics');

// API endpoints for automations
Route::get('/api/automations', [AutomationsController::class, 'index'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.index');

Route::get('/api/automations/{id}', [AutomationsController::class, 'show'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.show');

Route::put('/api/automations/{id}/toggle', [AutomationsController::class, 'toggle'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.toggle');

Route::post('/api/automations', [AutomationsController::class, 'store'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.store');

Route::put('/api/automations/{id}', [AutomationsController::class, 'update'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.update');

Route::delete('/api/automations/{id}', [AutomationsController::class, 'destroy'])
    ->middleware(['verify.shopify'])
    ->name('api.automations.destroy');

// External API endpoint to get store/user by domain
Route::match(['GET', 'OPTIONS'], '/external/store', [ExternalStoreController::class, 'getStore'])
    ->name('external.store');

// Vue.js SPA catch-all route - must be LAST to catch all frontend routes
// This serves the Vue app for any route that doesn't match the above routes
Route::get('/{any}', [HomeController::class, 'index'])
    ->where('any', '.*')
    ->middleware(['verify.shopify'])
    ->name('spa');
