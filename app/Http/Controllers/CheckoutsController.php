<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Enums\CheckoutStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AppHelper;

class CheckoutsController extends Controller
{
    /**
     * Return abandoned checkouts for the authenticated shop.
     */
    public function getAbandonedCheckouts(): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        
        $checkouts = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::OPEN)
            ->where('checkout_created_at', '<', 
            Carbon::now()->subMinutes(AppHelper::isLocal() ? 0 : 5))
            ->orderByDesc('checkout_updated_at')
            ->get();

        $this->attachCartPermalink($checkouts);

        return response()->json([
            'success' => true,
            'data' => $checkouts,
        ]);
    }

    /**
     * Return recoveries (completed checkouts with a sent message) for the authenticated shop.
     */
    public function getRecoveries(): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $checkouts = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED->value)
            ->where('is_message_sent', true)
            ->orderByDesc('checkout_updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $checkouts,
        ]);
    }

    /**
     * Mark a checkout as message sent.
     */
    public function sendMessage(int $checkoutId): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $checkout = Checkout::where('user_id', $shop->id)
            ->where('id', $checkoutId)
            ->first();

        if (! $checkout) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout not found.',
            ], 404);
        }

        $checkout->is_message_sent = true;
        $checkout->save();

        return response()->json([
            'success' => true,
            'data' => $checkout,
        ]);
    }

    function attachCartPermalink($checkouts) {
        foreach ($checkouts as $checkout) {
            $checkout->cart_permalink = $this->getCartPermalink($checkout);
        }
    }

    function getCartPermalink($checkout) {
        // create a permalink from this formula
        // {% with event.extra.line_items as items %}{{ organization.url|trim_slash }}/cart/{% for item in items %}{{ item.variant_id }}:{{ item.quantity|floatformat:'0' }}{% if not forloop.last %},{% endif %}{% endfor %}{% endwith %}? checkout%5Bemail%5D={{ email }}
        if (empty($checkout->abandoned_checkout_url)) return;
        // get hostname from abandoned_checkout_url
        $permalink = $this->getHostUrl($checkout->abandoned_checkout_url);
        
        $permalink .= '/cart/';

        foreach ($checkout->data['line_items'] ?? [] as $item) {
            $permalink .= $item['variant_id'] . ':' . $item['quantity'] . ',';
        }
        $permalink .= '?checkout[email]=' . $checkout->email;
        $permalink .= '&checkout[phone]=' . $checkout->phone_number;
        return $permalink;
    }

    function getHostUrl($url) {
        $host = parse_url($url, PHP_URL_HOST); // e.g. "example.myshopify.com"
        return 'https://' . $host;
    }
}

