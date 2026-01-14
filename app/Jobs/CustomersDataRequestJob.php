<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Webhook job responsible for handling GDPR customer data requests.
 * 
 * When a customer requests their data, Shopify sends this webhook.
 * You should respond with the customer's data within 30 days.
 */
class CustomersDataRequestJob implements ShouldQueue
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
     * @return bool
     */
    public function handle(): bool
    {
        Log::info('GDPR Customer Data Request received', [
            'domain' => $this->domain,
            'customer_id' => $this->data->customer->id ?? null,
            'customer_email' => $this->data->customer->email ?? null,
            'orders_requested' => $this->data->orders_requested ?? false,
        ]);

        // TODO: Implement your logic to collect and return customer data
        // This should include:
        // - Customer information stored in your app
        // - Order data if orders_requested is true
        // - Any other data related to this customer
        
        // Example: You might want to store this request and process it asynchronously
        // or send the data directly to Shopify's customer data request endpoint

        return true;
    }
}
