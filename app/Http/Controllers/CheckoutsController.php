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
            ->where('status', CheckoutStatus::OPEN->value)
            ->where('checkout_created_at', '<', 
            Carbon::now()->subMinutes(AppHelper::isLocal() ? 1 : 5))
            ->orderByDesc('checkout_updated_at')
            ->orderByDesc('created_at')
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
}

