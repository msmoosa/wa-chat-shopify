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

    /**
     * Return analytics data for checkouts for the authenticated shop.
     */
    public function getAnalytics(): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $now = Carbon::now();
        $last7Days = $now->copy()->subDays(7);
        $last30Days = $now->copy()->subDays(30);

        // Total checkouts
        $totalCheckouts = Checkout::where('user_id', $shop->id)->count();
        
        // Abandoned checkouts (open status)
        $abandonedCheckouts = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::OPEN)
            ->count();
        
        // Completed checkouts
        $completedCheckouts = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->count();
        
        // Recovered checkouts (completed with message sent)
        $recoveredCheckouts = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('is_message_sent', true)
            ->count();
        
        // Total revenue from completed checkouts
        $totalRevenue = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->sum('total_price');
        
        // Revenue from recovered checkouts
        $recoveredRevenue = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('is_message_sent', true)
            ->sum('total_price');
        
        // Last 7 days metrics
        $checkoutsLast7Days = Checkout::where('user_id', $shop->id)
            ->where('checkout_created_at', '>=', $last7Days)
            ->count();
        
        $abandonedLast7Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::OPEN)
            ->where('checkout_created_at', '>=', $last7Days)
            ->count();
        
        $recoveredLast7Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('is_message_sent', true)
            ->where('checkout_created_at', '>=', $last7Days)
            ->count();
        
        $revenueLast7Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('checkout_created_at', '>=', $last7Days)
            ->sum('total_price_usd');
        
        // Last 30 days metrics
        $checkoutsLast30Days = Checkout::where('user_id', $shop->id)
            ->where('checkout_created_at', '>=', $last30Days)
            ->count();
        
        $abandonedLast30Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::OPEN)
            ->where('checkout_created_at', '>=', $last30Days)
            ->count();
        
        $recoveredLast30Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('is_message_sent', true)
            ->where('checkout_created_at', '>=', $last30Days)
            ->count();
        
        $revenueLast30Days = Checkout::where('user_id', $shop->id)
            ->where('status', CheckoutStatus::COMPLETED)
            ->where('checkout_created_at', '>=', $last30Days)
            ->sum('total_price_usd');
        
        // Calculate recovery rate
        $recoveryRate = $abandonedCheckouts > 0 
            ? round(($recoveredCheckouts / $abandonedCheckouts) * 100, 2) 
            : 0;
        
        // Average cart value
        $averageCartValue = $completedCheckouts > 0 
            ? round($totalRevenue / $completedCheckouts, 2) 
            : 0;
        
        // Average recovered cart value
        $averageRecoveredCartValue = $recoveredCheckouts > 0 
            ? round($recoveredRevenue / $recoveredCheckouts, 2) 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => [
                    'total_checkouts' => $totalCheckouts,
                    'abandoned_checkouts' => $abandonedCheckouts,
                    'completed_checkouts' => $completedCheckouts,
                    'recovered_checkouts' => $recoveredCheckouts,
                    'total_revenue_usd' => round($totalRevenue, 2),
                    'recovered_revenue_usd' => round($recoveredRevenue, 2),
                    'recovery_rate_percent' => $recoveryRate,
                    'average_cart_value_usd' => $averageCartValue,
                    'average_recovered_cart_value_usd' => $averageRecoveredCartValue,
                ],
                'last_7_days' => [
                    'total_checkouts' => $checkoutsLast7Days,
                    'abandoned_checkouts' => $abandonedLast7Days,
                    'recovered_checkouts' => $recoveredLast7Days,
                    'revenue_usd' => round($revenueLast7Days, 2),
                ],
                'last_30_days' => [
                    'total_checkouts' => $checkoutsLast30Days,
                    'abandoned_checkouts' => $abandonedLast30Days,
                    'recovered_checkouts' => $recoveredLast30Days,
                    'revenue_usd' => round($revenueLast30Days, 2),
                ],
            ],
        ]);
    }
}

