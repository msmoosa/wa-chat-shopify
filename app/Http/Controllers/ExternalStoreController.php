<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;

class ExternalStoreController extends Controller
{
    /**
     * Get shop/user information by domain.
     *
     * @param Request $request
     * @param IShopQuery $shopQuery
     * @return JsonResponse
     */
    public function getStore(Request $request, IShopQuery $shopQuery): JsonResponse
    {
        $shopDomain = $request->query('shop');

        if (!$shopDomain) {
            return response()->json([
                'success' => false,
                'message' => 'Shop parameter is required. Use ?shop=domain.myshopify.com'
            ], 400);
        }

        // Normalize the domain
        $shopDomain = strtolower(trim($shopDomain));
        
        // Ensure it ends with .myshopify.com
        if (!str_ends_with($shopDomain, '.myshopify.com')) {
            $shopDomain .= '.myshopify.com';
        }

        try {
            $shop = $shopQuery->getByDomain(ShopDomain::fromNative($shopDomain));

            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shop not found'
                ], 404);
            }

            // Return shop data (exclude sensitive information)
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $shop->getId(),
                    'domain' => (string) $shop->getDomain()->toNative(),
                    'name' => $shop->name ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving shop: ' . $e->getMessage()
            ], 500);
        }
    }
}
