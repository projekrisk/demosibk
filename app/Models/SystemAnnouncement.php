<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAnnouncement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'is_active',
        'color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}