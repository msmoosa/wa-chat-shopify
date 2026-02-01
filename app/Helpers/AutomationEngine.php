<?php

namespace App\Helpers;

use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\AutomationStepRun;
use App\Jobs\ExecuteAutomationStep;
use App\Models\Checkout;
use App\Jobs\ExecuteAutomationStepJob;
use App\Services\AppHelper;

class AutomationEngine
{
    public static function startForCheckout(Checkout $checkout)
    {
        logger()->info('Starting automation for checkout: ' . $checkout->id);
        $automations = Automation::where('trigger', 'abandoned_checkout')
            ->where('is_active', true)
            ->with('steps')
            ->get();

        foreach ($automations as $automation) {
            self::scheduleSteps($automation, $checkout);
        }
    }

    protected static function scheduleSteps($automation, $checkout)
    {
        $runAt = now();
        // TODO: Add abandoned checkout delay
        if ($automation->trigger === 'abandoned_checkout') {
            $runAt = $runAt->addMinutes(AppHelper::isLocal() ? 1 : 5);
        }

        foreach ($automation->steps->sortBy('step_order') as $step) {

            $runAt = $runAt->addMinutes($step->wait_time);

            $stepRun = AutomationStepRun::create([
                'automation_id'      => $automation->id,
                'automation_step_id' => $step->id,
                'checkout_id'        => $checkout->id,
                'channel'            => $step->type === 'send_whatsapp' ? 'whatsapp' : 'sms',
                'status'             => 'pending',
                'scheduled_at'       => $runAt,
            ]);

            logger()->info('Scheduling automation step: ' . $stepRun->id . ' - ' . $stepRun->status->value);
            ExecuteAutomationStep::dispatch($stepRun)
                ->delay($runAt);
        }
    }
}