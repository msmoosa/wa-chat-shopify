<?php

namespace App\Models\Enums;

enum AutomationStepType: string
{
    case DELAY = 'delay';
    case SEND_WHATSAPP = 'send_whatsapp';
    case SEND_SMS = 'send_sms';
}
