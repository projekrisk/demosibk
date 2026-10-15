<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentReport extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'counselor_id',
        'academic_year',
        'semester',
        'total_achievements',
        'total_violations',
        'total_counseling',
        
        'assessment_pribadi',
        'assessment_sosial',
        'assessment_belajar',
        'assessment_karir',

        'personality_development',
        'social_development',
        'academic_development',
        'general_conclusion',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}