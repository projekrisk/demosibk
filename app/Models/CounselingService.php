<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Facades\Filament;

class CounselingService extends Model
{
    protected $fillable = [
        'tenant_id', 'student_id', 'counselor_id',
        'service_type', 'target_class', 'status', 'occurred_at',
        'topic', 'description', 'result',
        'method', 'media', 'approach', 'parties_involved', 'location_description', 'process_evaluation',
        'student_class', 'academic_year', 'semester'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            if ($service->student_id && !$service->student_class) {
                $student = Student::find($service->student_id);
                if ($student) {
                    $service->student_class = $student->class_name;
                }
            }

            try {
                $tenant = Filament::getTenant();
                if ($tenant) {
                    if (!$service->academic_year) {
                        $service->academic_year = $tenant->current_academic_year;
                    }
                    if (!$service->semester) {
                        $service->semester = $tenant->current_semester;
                    }
                }
            } catch (\Exception $e) {
            }
            
            if (!$service->academic_year && $service->occurred_at) {
                $date = $service->occurred_at;
                $year = $date->year;
                if ($date->month >= 7) {
                    $service->academic_year = $year . '/' . ($year + 1);
                    $service->semester = 'Ganjil';
                } else {
                    $service->academic_year = ($year - 1) . '/' . $year;
                    $service->semester = 'Genap';
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'counseling_service_student');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}