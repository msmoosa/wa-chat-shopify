<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\ShopifyService;
use App\Services\CheckoutService;

class FetchCheckouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkouts:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch checkout from shopify
        // Batch save them to database
        $shop = User::whereName( 'msm5-2.myshopify.com')->first();
        $shopifyService = new ShopifyService($shop);
        $checkouts = $shopifyService->getCheckouts();
        //start a db transaction
        $checkoutService = new CheckoutService();
        $checkoutService->saveCheckouts($shop, $checkouts);
    }
}
