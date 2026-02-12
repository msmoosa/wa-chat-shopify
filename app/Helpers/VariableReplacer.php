<?php
namespace App\Helpers;

use App\Models\Checkout;

class VariableReplacer
{
    public static function replace(Checkout $checkout, string $message)
    {
        $message = str_replace('{customer_name}',$checkout->customer_name, $message);
        // TODO: shorten checkout url
        $message = str_replace('{checkout_url}',$checkout->abandoned_checkout_url, $message);
        return $message;
    }
}