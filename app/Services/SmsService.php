<?php
namespace App\Services;

use \ClickSend\Configuration;
use GuzzleHttp\Client;
use App\Services\TwilioService;
use App\Services\ClickSendService;
use App\Models\Checkout;
use App\Helpers\VariableReplacer;
class SmsService
{
    public $twilio;
    public $clicksend;
    public function __construct()
    {
        $this->twilio = new TwilioService();
        $this->clicksend = new ClickSendService();
    }
    public function send(Checkout $checkout, string $message)
    {
        // Replace variables in message
        $message = VariableReplacer::replace($checkout,$message);
        // TODO: Route message based on country code
        $this->twilio->send($phone, $message);
    }
}