<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'message',
        'user_id',
    ];
}

