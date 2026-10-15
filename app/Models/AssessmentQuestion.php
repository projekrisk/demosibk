<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestion extends Model
{
    protected $fillable = [
        'tenant_id',
        'assessment_package_id',
        'category',
        'type',
        'question_text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    public function package(): BelongsTo
    {
        return $this->belongsTo(AssessmentPackage::class, 'assessment_package_id');
    }
}