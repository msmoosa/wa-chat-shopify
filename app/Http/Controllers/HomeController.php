<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\AfterAuthenticateJob;
use Osiset\ShopifyApp\Objects\Values\ShopId;
use Osiset\ShopifyApp\Messaging\Jobs\WebhookInstaller;

class HomeController extends Controller
{
    function index() {
        $shop = Auth::user();
        $shopDomain = $shop->name ?? '';
        // dispatch a job to WebhookInstaller
        AfterAuthenticateJob::dispatch($shop);
        

        return view('welcome', compact('shop', 'shopDomain'));
    }
}
