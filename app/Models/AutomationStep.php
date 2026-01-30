<?php

namespace App\Models;

use App\Models\Enums\AutomationStepType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationStep extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'automation_id',
        'type',
        'config',
        'step_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AutomationStepType::class,
            'config' => 'array',
            'step_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the automation that owns the step.
     */
    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    /**
     * Get the step runs for this step.
     */
    public function stepRuns(): HasMany
    {
        return $this->hasMany(AutomationStepRun::class);
    }
}
