<?php

namespace App\Jobs;

use Osiset\ShopifyApp\Contracts\Commands\Shop;
use Osiset\ShopifyApp\Contracts\Queries\Shop as QueriesShop;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    public function handle(Shop $shopCommand, QueriesShop $shopQuery, CancelCurrentPlan $cancelCurrentPlanAction): bool
    {
        $shop = $shopQuery->getByDomain($this->domain);

        $parentResult = parent::handle($shopCommand, $shopQuery, $cancelCurrentPlanAction);
        SlackNotificationJob::dispatchSync('uninstall', $shop);
        return $parentResult;
    }
}
