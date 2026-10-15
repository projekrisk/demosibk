<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAssessment extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'assessment_package_id', // BARU
        'academic_year',
        'filled_at',
        'percentage_pribadi',
        'percentage_sosial',
        'percentage_belajar',
        'percentage_karir',
        
        // FIELD BARU: Skor RIASEC
        'score_r',
        'score_i',
        'score_a',
        'score_s',
        'score_e',
        'score_c',

        'counselor_notes',
        'status', // 'draft' (sedang isi), 'completed' (selesai)
    ];

    protected $casts = [
        'filled_at' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    public function package(): BelongsTo
    {
        return $this->belongsTo(AssessmentPackage::class, 'assessment_package_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class);
    }

    // FITUR BARU: Algoritma Cerdas Penentu Jurusan
    public function getRiasecResultAttribute()
    {
        if (!$this->package || $this->package->usage_type !== 'riasec') {
            return null;
        }

        $scores = [
            'R' => $this->score_r,
            'I' => $this->score_i,
            'A' => $this->score_a,
            'S' => $this->score_s,
            'E' => $this->score_e,
            'C' => $this->score_c,
        ];

        // Urutkan skor dari yang paling besar ke kecil
        arsort($scores);
        
        // Ambil 3 huruf tertinggi (Holland Code)
        $top3 = array_slice(array_keys($scores), 0, 3);
        $code = implode('-', $top3);

        // Mapping Jurusan berdasarkan huruf pertama (Paling Dominan)
        $primary = $top3[0];
        $recommendation = match($primary) {
            'R', 'I' => 'IPA (Sains & Teknologi)',
            'A' => 'BAHASA (Seni & Sastra)',
            'S', 'E', 'C' => 'IPS (Sosial & Humaniora)',
            default => 'Belum Terpetakan'
        };

        return [
            'code' => $code,
            'recommendation' => $recommendation
        ];
    }
}