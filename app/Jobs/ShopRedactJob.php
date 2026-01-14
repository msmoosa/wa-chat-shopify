<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

/**
 * Webhook job responsible for handling GDPR shop data redaction.
 * 
 * When a shop owner requests their shop data to be deleted, Shopify sends this webhook.
 * You must delete all shop data from your systems.
 */
class ShopRedactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param string   $domain The shop domain.
     * @param stdClass $data   The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct(string $domain, stdClass $data)
    {
        $this->domain = $domain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param IShopQuery $shopQuery The querier for shops.
     *
     * @return bool
     */
    public function handle(IShopQuery $shopQuery): bool
    {
        Log::info('GDPR Shop Redaction received', [
            'domain' => $this->domain,
            'shop_id' => $this->data->shop_id ?? null,
            'shop_domain' => $this->data->shop_domain ?? null,
        ]);

        // Convert the domain
        $shopDomain = ShopDomain::fromNative($this->domain);

        // Get the shop
        $shop = $shopQuery->getByDomain($shopDomain);
        if (!$shop) {
            Log::warning('Shop not found for redaction', ['domain' => $this->domain]);
            return true;
        }

        // TODO: Implement your logic to delete all shop data
        // This should include:
        // - Delete all shop-related records from your database
        // - Delete all customer data associated with this shop
        // - Delete all order data associated with this shop
        // - Delete any files, logs, or other data related to this shop
        // - Ensure data is permanently deleted (not just soft deleted)
        
        // Example:
        // Order::where('shop_id', $shop->id)->forceDelete();
        // Customer::where('shop_id', $shop->id)->forceDelete();
        // Product::where('shop_id', $shop->id)->forceDelete();
        // $shop->forceDelete(); // Permanently delete the shop record

        return true;
    }
}
