<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    private $client;
    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }
    public function send($to, $message)
    {
        $this->client->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            "messagingServiceSid" => "MGe526fb50644801195e57ab3c236f7f2a",
            'body' => $message,
        ]);
    }
}