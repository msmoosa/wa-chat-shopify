<?php namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;
use App\Models\User;
use App\Services\CheckoutService;

class CheckoutsUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Convert domain
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);

        logger()->info('[CheckoutsUpdateJob] Data: ' . json_encode($this->data));
        $shopDomain = $this->shopDomain->toNative();
        $user = User::whereName($shopDomain)->first();
        if (!$user) {
            logger()->error('[CheckoutsUpdateJob] User not found for shop domain: ' . $shopDomain);
            return;
        }

        $checkoutService = new CheckoutService();
        $checkoutArray = json_decode(json_encode($this->data), true);
        logger()->info('[CheckoutsUpdateJob] Checkout Array: ' . json_encode($checkoutArray, JSON_PRETTY_PRINT));
        $checkoutService->saveCheckout($user, $checkoutArray);
    }
}
