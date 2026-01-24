<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index() {
        $shop = auth()->user();
        $shopDomain = $shop->name ?? '';
        $productsResponse = $shop->api()->rest('GET', '/admin/api/2026-01/products.json');
        logger()->info('Products: ' . json_encode($productsResponse));
        $products = $productsResponse['body']['products'] ?? [];
        $error = $productsResponse['errors'] ? $productsResponse['body']: '';
        return view('welcome', compact('products', 'error', 'shopDomain'));
    }
}
