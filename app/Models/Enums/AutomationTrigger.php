<?php

namespace App\Models\Enums;

enum AutomationTrigger: string
{
    case ABANDONED_CHECKOUT = 'abandoned_checkout';
    case ORDER_CREATED = 'order_created';
    case ORDER_UPDATED = 'order_updated';
    case CHECKOUT_CREATED = 'checkout_created';
    case CHECKOUT_UPDATED = 'checkout_updated';
}
