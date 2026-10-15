<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMessage extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'counselor_id',
        'sender_name',
        'subject',
        'message',
        'is_anonymous',
        'response',
        'replied_at',
        'replied_by',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function targetCounselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    
    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}