<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $shop = auth()->user();
    $productsResponse = $shop->api()->rest('GET', '/admin/api/2026-01/products.json');
    logger()->info('Products: ' . json_encode($productsResponse));
    $products = $productsResponse['body']['products'] ?? [];
    $error = $productsResponse['errors'] ? $productsResponse['body']: '';
    return view('welcome', compact('products', 'error'));
})->middleware(['verify.shopify'])->name('home');
