<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopConfigController extends Controller
{
    /**
     * Get the authenticated shop's configuration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConfig(Request $request): JsonResponse
    {
        $shop = Auth::user();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'whatsapp_config' => $shop->whatsapp_config ?? $this->getDefaultWhatsappConfig()
            ]
        ]);
    }

    /**
     * Save the authenticated shop's configuration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveConfig(Request $request): JsonResponse
    {
        $shop = Auth::user();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Validate the request
        $validated = $request->validate([
            'phone' => 'nullable|string|max:255',
            'isEnabled' => 'boolean',
            'position' => 'nullable|string|in:bottom-left,bottom-right,top-left,top-right',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            // Merge with existing config to preserve any additional fields
            $existingConfig = $shop->whatsapp_config ?? [];
            $whatsappConfig = array_merge($this->getDefaultWhatsappConfig(), $existingConfig, $validated);

            // Update the shop's whatsapp_config
            $shop->whatsapp_config = $whatsappConfig;
            /** @var \App\Models\User $shop */
            $shop->save();

            return response()->json([
                'success' => true,
                'message' => 'Configuration saved successfully',
                'data' => [
                    'whatsapp_config' => $shop->whatsapp_config
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDefaultWhatsappConfig()
    {
        return [
            'phone' => '',
            'isEnabled' => true,
            'position' => 'bottom-right',
            'color' => '#42D74C',
        ];
    }
}
