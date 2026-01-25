<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExternalStoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['verify.shopify'])->name('home');

// External API endpoint to get store/user by domain
Route::get('/external/store', [ExternalStoreController::class, 'getStore'])
    ->name('external.store');
