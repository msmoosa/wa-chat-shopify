<?php

namespace App\Models;

use App\Models\Enums\AutomationStepRunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationStepRun extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'automation_id',
        'automation_step_id',
        'checkout_id',
        'channel',
        'status',
        'scheduled_at',
        'sent_at',
        'provider',
        'provider_message_id',
        'cost',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AutomationStepRunStatus::class,
            'cost' => 'decimal:4',
            'meta' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the automation that owns the step run.
     */
    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    /**
     * Get the automation step for this run.
     */
    public function automationStep(): BelongsTo
    {
        return $this->belongsTo(AutomationStep::class);
    }

    /**
     * Get the checkout for this step run.
     */
    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }
}
