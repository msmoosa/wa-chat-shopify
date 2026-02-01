<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AutomationStepRun;
use App\Models\Enums\AutomationStepRunStatus;


class ExecuteAutomationStep implements ShouldQueue
{
    use Queueable;

    public AutomationStepRun $stepRun;
    /**
     * Create a new job instance.
     */
    public function __construct(AutomationStepRun $stepRun) {
        $this->stepRun = $stepRun;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $stepRun = $this->stepRun->fresh();

        if ($stepRun->status != AutomationStepRunStatus::PENDING) {
            logger()->info('Automation step already executed: ' . $stepRun->id . ' - ' . $stepRun->status->value);
            return;
        }

        // order already placed?
        if ($stepRun->checkout->isOrder() && $stepRun->automation->trigger === 'abandoned_checkout') {
            logger()->info('Order already placed, skipping automation step: ' . $stepRun->id);
            $stepRun->update(['status' => 'skipped']);
            return;
        }

        logger()->info('Executing automation step: ' . $stepRun->automationStep->config['message']);
        // MessageSender::send(
        //     $stepRun->channel,
        //     $stepRun->checkout->phone,
        //     $stepRun->step->config
        // );

        $stepRun->update([
            'status'  => 'sent',
            'sent_at'=> now(),
        ]);
    }
}
