<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\Transaction; 

class Tenant extends Model
{
    protected $fillable = [
        'npsn',
        'name',
        'slug',
        'logo_path',
        'subscription_status',
        'subscription_ends_at',
        'address',
        'headmaster_name',
        'headmaster_nip',
        'current_academic_year',
        'current_semester',
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(Level::class);
    }

    public function counselingServices(): HasMany
    {
        return $this->hasMany(CounselingService::class);
    }

    public function dailyJournals(): HasMany
    {
        return $this->hasMany(DailyJournal::class);
    }

    public function studentMessages(): HasMany
    {
        return $this->hasMany(StudentMessage::class);
    }

    public function studentReports(): HasMany
    {
        return $this->hasMany(StudentReport::class);
    }

    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    public function studentAssessments(): HasMany
    {
        return $this->hasMany(StudentAssessment::class);
    }

    public function assessmentPackages(): HasMany
    {
        return $this->hasMany(AssessmentPackage::class);
    }

    public function getLimits(): array
    {
        $defaultLimits = [
            'counselors' => 3, 
            'students' => 50, 
            'print_report' => false 
        ]; 

        if (!in_array($this->subscription_status, ['active', 'trial'])) {
            return $defaultLimits;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return $defaultLimits;
        }

        $lastPaidTransaction = Transaction::where('tenant_id', $this->id)
            ->where('status', 'paid')
            ->latest()
            ->first();
            
        $planName = $lastPaidTransaction ? $lastPaidTransaction->plan_type : 'Starter';

        return match ($planName) {
            'Starter', 'Paket Uji Coba' => [
                'counselors' => 3, 
                'students' => 50, 
                'print_report' => true
            ],
            'Pro Semester', 'Paket Semester' => [
                'counselors' => 6, 
                'students' => 500, 
                'print_report' => true
            ],
            'Enterprise', 'Paket Tahunan'  => [
                'counselors' => null,
                'students' => null, 
                'print_report' => true
            ], 
            default => [
                'counselors' => 3, 
                'students' => 50, 
                'print_report' => true
            ],
        };
    }

    public function getCounselorLimit(): ?int
    {
        return $this->getLimits()['counselors'];
    }

    public function hasCounselorQuota(): bool
    {
        $limit = $this->getLimits()['counselors'];
        
        if ($limit === null) return true; 

        $currentCounselorCount = $this->members()
            ->where('role', 'konselor')
            ->count();

        return $currentCounselorCount < $limit;
    }

    public function hasStudentQuota(): bool
    {
        $limit = $this->getLimits()['students'];
        if ($limit === null) return true; 

        $currentStudentCount = $this->students()->count();

        return $currentStudentCount < $limit;
    }

    public function canPrintReport(): bool
    {
        return $this->getLimits()['print_report'] ?? false;
    }
    
    public function personalAssessmentPackages(): HasMany
    {
        return $this->assessmentPackages()->where('category', 'Pribadi');
    }

    public function socialAssessmentPackages(): HasMany
    {
        return $this->assessmentPackages()->where('category', 'Sosial');
    }

    public function careerAssessmentPackages(): HasMany
    {
        return $this->assessmentPackages()->where('category', 'Karir');
    }

    public function studyAssessmentPackages(): HasMany
    {
        return $this->assessmentPackages()->where('category', 'Belajar');
    }
}