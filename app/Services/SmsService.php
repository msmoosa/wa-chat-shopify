<?php
namespace App\Services;

use \ClickSend\Configuration;
use GuzzleHttp\Client;
class SmsService
{
    public $config;
    public function __construct()
    {
        $this->config = Configuration::getDefaultConfiguration()
            ->setUsername(env('CLICKSEND_USERNAME'))
            ->setPassword(env('CLICKSEND_API_KEY'));
    }
    public function send(string $phone, string $message)
    {
        $apiInstance = new \ClickSend\Api\SMSApi(new Client(),$this->config);

        $msg = new \ClickSend\Model\SmsMessage();
        $msg->setSource(config('app.name'));
        $msg->setBody($message);
        $msg->setTo($phone);

        $sms_messages = new \ClickSend\Model\SmsMessageCollection();
        $sms_messages->setMessages([$msg]);

        try {
            $result = $apiInstance->smsSendPost($sms_messages);
            logger()->info('SMS sent successfully: ' . json_encode($result));
            return true;
        } catch (\Exception $e) {
            logger()->error('Exception when calling SMSApi->smsSendPost: ' . $e->getMessage());
        }
    }
}