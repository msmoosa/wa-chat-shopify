<?php

namespace App\Jobs;

use Osiset\ShopifyApp\Contracts\Commands\Shop;
use Osiset\ShopifyApp\Contracts\Queries\Shop as QueriesShop;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use App\Models\User;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    public function handle(Shop $shopCommand, QueriesShop $shopQuery, CancelCurrentPlan $cancelCurrentPlanAction): bool
    {
        $shop = $shopQuery->getByDomain($this->domain);

        // Reset onboarding flag before handling uninstall
        // This ensures if the shop reinstalls, they'll see the onboarding again
        if ($shop) {
            // Find the user by domain name (same pattern as AfterAuthenticateJob)
            $user = User::where('name', $shop->getDomain()->toNative())->first();
            if ($user) {
                $user->onboarding_completed = false;
                $user->save();
                logger()->info('Onboarding flag reset for uninstalled shop', [
                    'shop_domain' => $shop->getDomain()->toNative(),
                    'user_id' => $user->id
                ]);
            }
        }

        $parentResult = parent::handle($shopCommand, $shopQuery, $cancelCurrentPlanAction);
        SlackNotificationJob::dispatchSync('uninstall', $shop);
        return $parentResult;
    }
}
