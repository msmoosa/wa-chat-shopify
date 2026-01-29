<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use App\Models\User;
use App\Models\ManualTemplate;
use App\Jobs\SlackNotificationJob;
class AfterAuthenticateJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The shop model.
     *
     * @var IShopModel
     */
    public $shop;

    /**
     * Create a new job instance.
     *
     * @param IShopModel $shop The shop model
     */
    public function __construct(IShopModel $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('AfterAuthenticateJob triggered', [
            'shop_id' => $this->shop->getId(),
            'shop_domain' => (string) $this->shop->getDomain()->toNative(),
        ]);

        $user = User::whereName($this->shop->getDomain()->toNative())->first();

        // Add your logic here after shop authentication
        // For example, you can install script tags, create webhooks, etc.
        // install script tags from config/shopify-app.php
        $scriptTags = config('shopify-app.scripttags');
        logger()->debug('ScriptTags To Install: ', ['scriptTags' => $scriptTags]);

        // check if script tags are already installed
        // $installedScriptTags = $this->shop->apiHelper()->getScriptTags();
        // logger()->debug('Installed ScriptTags', ['installedScriptTags' => $installedScriptTags]);
        // $installedScriptTags = $installedScriptTags->toArray();
        // foreach ($scriptTags as $scriptTag) {
        //     if (!in_array($scriptTag['src'], $installedScriptTags)) {
        //         $response = $this->shop->apiHelper()->createScriptTag($scriptTag);
        //         logger()->debug('ScriptTag created', ['response' => $response]);
        //     }
        // }

        // install webhooks from config/shopify-app.php
        // $webhooks = config('shopify-app.webhooks');
        // logger()->debug('Webhooks To Install: ', ['count' => count($webhooks), 'webhooks' => $webhooks]);
        // $installedWebhooks = $this->shop->apiHelper()->getWebhooks();
        // logger()->debug('Installed Webhooks', ['count' => count($installedWebhooks), 'installedWebhooks' => $installedWebhooks]);
        // $installedWebhooks = $installedWebhooks->toArray();
        // $installedWebhooksJson = json_encode($installedWebhooks);
        // foreach ($webhooks as $webhook) {
        //     if (!str_contains($installedWebhooksJson, $webhook['topic'])) {
        //         $response = $this->shop->apiHelper()->createWebhook($webhook);
        //         logger()->debug('Webhook created for topic: ' . $webhook['topic'], ['response' => $response]);
        //     }
        // }

        $this->addDefaultMessageTemplates($user);
        SlackNotificationJob::dispatch('config_saved', $user);
    }

    private function addDefaultMessageTemplates(User $user)
    {
        if ($user->manualTemplates()->count() > 0) {
            return;
        }
        $messageTemplates = [
            'Cart Reminder' => 'Hello {customer_name}, we noticed you left some items in your cart. Would you like to complete your purchase? {checkout_url}',
        ];
        foreach ($messageTemplates as $title => $message) {
            $manualTemplate = new ManualTemplate();
            $manualTemplate->title = $title;
            $manualTemplate->message = $message;
            $manualTemplate->user_id = $user->id;
            $manualTemplate->save();
            logger()->debug('Default message template created', ['title' => $title, 'message' => $message, 'user_id' => $user->id]);
        }
    }
}
