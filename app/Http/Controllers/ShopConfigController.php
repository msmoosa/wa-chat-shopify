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

        $whatsappConfig = array_merge($this->getDefaultWhatsappConfig(), $shop->whatsapp_config ?? []);
        $shop->whatsapp_config = $whatsappConfig;
        return response()->json([
            'success' => true,
            'data' => $shop
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

        try {
            // Merge with existing config to preserve any additional fields
            $whatsappConfig = $request->all();

            // Update the shop's whatsapp_config
            $shop->whatsapp_config = $whatsappConfig;
            /** @var \App\Models\User $shop */
            $shop->save();

            return response()->json([
                'success' => true,
                'message' => 'Configuration saved successfully',
                'data' => $shop
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
            'phoneNumber' => '',
            'isEnabled' => true,
            'buttonPosition' => 'bottom-right',
            'buttonBackgroundColor' => '#42D74C',
            'buttonIconColor' => '#FFFFFF',
            'buttonTextColor' => '#FFFFFF',
            'buttonTextContent' => 'Contact Us',
            'designType' => 'icon'
        ];
    }
}
