<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index() {
        $shop = auth()->user();
        $shopDomain = $shop->name ?? '';
        return view('welcome', compact('shop', 'shopDomain'));
    }
}
