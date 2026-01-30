<?php

namespace App\Models\Enums;

enum AutomationStepRunStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';
}
