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
        logger()->info('AppUninstalled Job triggered');
        $user = User::where('name', $this->domain)->first();
        if ($user) {
            $user->onboarding_completed = false;
            $user->save();
            logger()->info('Onboarding flag reset for uninstalled shop', [
                'shop_domain' => $this->domain,
                'user_id' => $user->id
            ]);
        }

        $parentResult = parent::handle($shopCommand, $shopQuery, $cancelCurrentPlanAction);
        SlackNotificationJob::dispatchSync('uninstall', $user);
        return $parentResult;
    }
}
