<?php

namespace App\Models;

use App\Models\Enums\AutomationTrigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Automation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'trigger',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trigger' => AutomationTrigger::class,
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user (shop) that owns the automation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the steps for the automation.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(AutomationStep::class)->orderBy('step_order');
    }

    /**
     * Get the step runs for the automation.
     */
    public function stepRuns(): HasMany
    {
        return $this->hasMany(AutomationStepRun::class);
    }
}
