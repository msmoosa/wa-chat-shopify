<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkout extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shopify_checkout_id',
        'checkout_token',
        'cart_token',
        'abandoned_checkout_url',
        'order_id',
        'total_price',
        'currency',
        'total_price_usd',
        'status',
        'is_message_sent',
        'customer_name',
        'phone_number',
        'email',
        'buyer_accepts_marketing',
        'tags',
        'data',
        'checkout_created_at',
        'checkout_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'total_price_usd' => 'decimal:2',
            'buyer_accepts_marketing' => 'boolean',
            'is_message_sent' => 'boolean',
            'data' => 'array',
            'checkout_created_at' => 'datetime',
            'checkout_updated_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user (shop) that owns the checkout.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the automation step runs for this checkout.
     */
    public function automationStepRuns(): HasMany
    {
        return $this->hasMany(AutomationStepRun::class);
    }
}
