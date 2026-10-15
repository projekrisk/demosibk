<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration_label',
        'duration_in_days',
        'description',
        'features',
        'is_featured',
        'is_active',
        'sort_order',
        'button_label',
        'button_url',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'integer',
        'duration_in_days' => 'integer',
    ];
}