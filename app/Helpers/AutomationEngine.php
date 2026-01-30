<?php

namespace App\Helpers;

use App\Models\Automation;
use App\Models\AutomationStep;
use App\Models\AutomationStepRun;
use App\Jobs\ExecuteAutomationStep;
use App\Models\Checkout;
use App\Jobs\ExecuteAutomationStepJob;

class AutomationEngine
{
    public static function startForCheckout(Checkout $checkout)
    {
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

        foreach ($automation->steps->sortBy('step_order') as $step) {

            if ($step->type === 'delay') {
                $runAt = $runAt->addMinutes($step->config['minutes']);
                continue;
            }

            $stepRun = AutomationStepRun::create([
                'automation_id'      => $automation->id,
                'automation_step_id' => $step->id,
                'checkout_id'        => $checkout->id,
                'channel'            => $step->type === 'send_whatsapp' ? 'whatsapp' : 'sms',
                'status'             => 'pending',
                'scheduled_at'       => $runAt,
            ]);

            ExecuteAutomationStep::dispatch($stepRun)
                ->delay($runAt);
        }
    }
}