<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AutomationStepRun;


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

        if ($stepRun->status !== 'pending') return;

        // order already placed?
        if ($stepRun->checkout->order_created_at) {
            $stepRun->update(['status' => 'skipped']);
            return;
        }

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
