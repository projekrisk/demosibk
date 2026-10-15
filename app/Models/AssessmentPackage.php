<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentPackage extends Model
{
    protected $fillable = [
        'tenant_id',
        'title',
        'category',
        'usage_type',
        'description',
        'is_active',
        'target_classrooms', // FIELD BARU: Target Kelas (Array JSON)
        'scheduled_at', 
        'deadline_at',  
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_classrooms' => 'array', // Pastikan diconvert sebagai Array
        'scheduled_at' => 'datetime',
        'deadline_at' => 'datetime', 
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }
}