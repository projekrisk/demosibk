<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentAnswer extends Model
{
    protected $fillable = [
        'student_assessment_id',
        'assessment_question_id',
        'answer_value',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}