<?php
namespace App\Models\Enums;

enum CheckoutStatus: string
{
    case OPEN = 'open';
    case COMPLETED = 'completed';
}