<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRecord extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'title',
        'description',
        'points',
        'recorded_at',
        'recorded_by',
    ];

    protected $casts = [
        'recorded_at' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}