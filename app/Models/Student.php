<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // WAJIB AUTHENTICATABLE
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'tenant_id', 
        'classroom_id',
        'nis', 
        'nisn', 
        'name', 
        'class_name', 
        'status', 
        'points', 
        'avatar_url',
        'password',
        'fcm_token'
    ];

    protected $hidden = [
        'password',
        'fcm_token',
    ];

    protected static function booted()
    {
        static::saving(function ($student) {
            // Hash password otomatis
            if ($student->isDirty('password') && $student->password) {
                $student->password = Hash::make($student->password);
            }

            if ($student->classroom_id) {
                $classroom = Classroom::find($student->classroom_id);
                if ($classroom) {
                    $student->class_name = $classroom->name;
                }
            } elseif (empty($student->class_name)) {
                $student->class_name = '-';
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function counselingServices(): HasMany
    {
        return $this->hasMany(CounselingService::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StudentMessage::class);
    }
}