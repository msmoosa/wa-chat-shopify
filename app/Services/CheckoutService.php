<?php
namespace App\Services;

use App\Models\Checkout;
use Illuminate\Support\Facades\DB;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use App\Models\Enums\CheckoutStatus;
use App\Models\User;
use Carbon\Carbon;

class CheckoutService
{
    public function getCheckouts()
    {
        return Checkout::all();
    }

    public function saveCheckouts(User $user, $checkouts)
    {
        DB::transaction(function () use ($checkouts, $user) {
            foreach ($checkouts as $checkout) {
                $this->saveCheckout($checkout, $user);
            }
            
        });
    }

    function saveCheckout($user, $checkout, $isOrder = false)
    {
        return Checkout::upsert([
            'user_id' => $user->id,
            'checkout_token' => $checkout['token'],
            'cart_token' => $checkout['cart_token'],
            'abandoned_checkout_url' => $checkout['abandoned_checkout_url'] ?? null,    
            'shopify_checkout_id' => $checkout['id'],
            'order_id' => $checkout['order_number'] ?? null,
            'total_price' => $checkout['total_price'],
            'currency' => $checkout['currency'],
            //'total_price_usd' => $checkout['total_price_usd'], //TODO: add total price in usd
            'status' => $isOrder ? CheckoutStatus::COMPLETED : CheckoutStatus::OPEN,
            'customer_name' => $checkout['shipping_address']['first_name'] . ' ' . $checkout['shipping_address']['last_name'],
            'phone_number' => $checkout['phone'],
            'email' => $checkout['email'],
            'buyer_accepts_marketing' => $checkout['buyer_accepts_marketing'],
            'tags' => '',
            'data' => json_encode($checkout),
            'checkout_created_at' => Carbon::parse($checkout['created_at'])->toDateTimeString(),
            'checkout_updated_at' => Carbon::parse($checkout['updated_at'])->toDateTimeString(),
        ], ['shopify_checkout_id']);
    }
}