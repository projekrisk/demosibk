<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentPackage;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StudentAssessmentController extends Controller
{
    public function getAvailablePackages(Request $request)
    {
        $user = $request->user();
        $now = now();

        $completedPackageIds = StudentAssessment::where('student_id', $user->id)
            ->where('status', 'completed')
            ->pluck('assessment_package_id')
            ->toArray();

        $packages = AssessmentPackage::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->whereNotIn('id', $completedPackageIds)
            ->where(function ($query) use ($now) {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('deadline_at')->orWhere('deadline_at', '>', $now);
            })
            ->get()
            ->map(function ($pkg) {
                return [
                    'id' => $pkg->id,
                    'title' => $pkg->title,
                    'description' => $pkg->description,
                    'total_questions' => $pkg->questions()->count(),
                    'deadline' => $pkg->deadline_at ? $pkg->deadline_at->format('d M Y H:i') : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $packages,
        ]);
    }

    public function getPackageQuestions(Request $request, $packageId)
    {
        $package = AssessmentPackage::findOrFail($packageId);
        
        if ($package->deadline_at && now()->gt($package->deadline_at)) {
             return response()->json(['message' => 'Maaf, batas waktu pengisian paket ini sudah berakhir.'], 403);
        }

        $cacheKey = "package_questions_{$packageId}";

        $segments = Cache::remember($cacheKey, 60 * 60, function () use ($package) {
            $questions = $package->questions()
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get();

            return $questions->groupBy('category')->map(function ($items, $category) {
                return [
                    'category_name' => $category,
                    'questions' => $items->map(function ($q) {
                        return [
                            'id' => $q->id,
                            'type' => $q->type,
                            'question' => $q->question_text,
                        ];
                    })->values()
                ];
            })->values();
        });

        return response()->json([
            'status' => 'success',
            'package_title' => $package->title,
            'segments' => $segments
        ]);
    }

    public function submitProgress(Request $request)
    {
        // Validasi value dilonggarkan (bisa string/int/boolean)
        $request->validate([
            'package_id' => 'required|exists:assessment_packages,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required',
            'answers.*.value' => 'required',
        ]);

        $user = $request->user();

        $assessment = StudentAssessment::firstOrCreate(
            [
                'tenant_id' => $user->tenant_id,
                'student_id' => $user->id,
                'assessment_package_id' => $request->package_id,
                'status' => 'draft'
            ],
            [
                'academic_year' => $user->tenant->current_academic_year ?? (date('Y') . '/' . (date('Y') + 1)),
                'filled_at' => now(),
            ]
        );

        foreach ($request->answers as $item) {
            StudentAssessmentAnswer::updateOrCreate(
                [
                    'student_assessment_id' => $assessment->id,
                    'assessment_question_id' => $item['question_id']
                ],
                [
                    // Pastikan disimpan sebagai string
                    'answer_value' => (string) $item['value'] 
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Progress tersimpan.'
        ]);
    }

    public function finalize(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:assessment_packages,id',
        ]);

        $user = $request->user();

        $assessment = StudentAssessment::where('student_id', $user->id)
            ->where('assessment_package_id', $request->package_id)
            ->where('status', 'draft')
            ->first();

        if (!$assessment) {
            return response()->json(['message' => 'Data asesmen tidak ditemukan atau sudah selesai.'], 404);
        }

        // Hitung ulang semua skor
        $this->calculateScores($assessment);

        $assessment->update(['status' => 'completed']);

        return response()->json([
            'status' => 'success',
            'message' => 'Asesmen selesai. Terima kasih!'
        ]);
    }

    /**
     * PERBAIKAN LOGIKA PERHITUNGAN SKOR (ROBUST)
     */
    private function calculateScores($assessment)
    {
        $usageType = $assessment->package->usage_type ?? 'diagnostic';

        // CABANG LOGIKA BARU: Jika ini adalah angket penjurusan (RIASEC)
        if ($usageType === 'riasec') {
            $scores = ['r' => 0, 'i' => 0, 'a' => 0, 's' => 0, 'e' => 0, 'c' => 0];
            $answers = $assessment->answers()->with('question')->get();

            foreach ($answers as $ans) {
                if ($ans->question) {
                    $cat = strtolower($ans->question->category); // r, i, a, s, e, atau c
                    if (array_key_exists($cat, $scores)) {
                        $val = 0;
                        if (is_numeric($ans->answer_value)) {
                            $val = (int) $ans->answer_value;
                        } elseif ($ans->answer_value === 'true') {
                            $val = 1;
                        }
                        $scores[$cat] += $val; // Jumlahkan skor mentah
                    }
                }
            }

            // Simpan skor RIASEC
            $assessment->update([
                'score_r' => $scores['r'],
                'score_i' => $scores['i'],
                'score_a' => $scores['a'],
                'score_s' => $scores['s'],
                'score_e' => $scores['e'],
                'score_c' => $scores['c'],
            ]);

            return; // Hentikan fungsi agar tidak menjalankan rumus AKPD di bawahnya
        }

        // Struktur Penampung Skor (LAMA - UNTUK AKPD/DCM)
        // Gunakan strtolower untuk kunci array agar case-insensitive
        $scores = [
            'pribadi' => ['total' => 0, 'max_score' => 0],
            'sosial'  => ['total' => 0, 'max_score' => 0],
            'belajar' => ['total' => 0, 'max_score' => 0],
            'karir'   => ['total' => 0, 'max_score' => 0],
        ];

        // Ambil semua jawaban
        $answers = $assessment->answers()->with('question')->get();

        foreach ($answers as $ans) {
            if ($ans->question) {
                // Normalisasi kategori ke huruf kecil (Pribadi -> pribadi)
                $cat = strtolower($ans->question->category); 
                $type = $ans->question->type;
                
                // Pastikan kategori ada di array scores
                if (array_key_exists($cat, $scores)) {
                    
                    // 1. Tentukan Nilai Jawaban (Konversi String ke Int dengan aman)
                    $val = 0;
                    if (is_numeric($ans->answer_value)) {
                        $val = (int) $ans->answer_value;
                    } elseif ($ans->answer_value === 'true') {
                        $val = 1;
                    }

                    // 2. Tentukan Skala Maksimal (Pembagi)
                    $maxScale = 1; // Default Checklist (0-1)

                    if ($type === 'likert' || $type === 'scale') {
                        $maxScale = 4; // Skala 1-4
                    } elseif ($type === 'rating') {
                        $maxScale = 10; // Skala 1-10
                    }
                    
                    // Abaikan soal teks/uraian dalam perhitungan angka
                    if ($type !== 'text' && $type !== 'open') {
                        $scores[$cat]['total'] += $val;
                        $scores[$cat]['max_score'] += $maxScale;
                    }
                }
            }
        }

        $updateData = [];
        foreach ($scores as $cat => $val) {
            if ($val['max_score'] > 0) {
                $field = 'percentage_' . $cat; // $cat sudah lowercase
                
                // RUMUS: (Total Skor / Total Max) * 100
                $percentage = ($val['total'] / $val['max_score']) * 100;
                $percentage = round($percentage); 
                
                if ($percentage > 100) $percentage = 100;
                
                $updateData[$field] = $percentage;
            } else {
                 // Jika tidak ada soal di kategori itu, set 0
                 $updateData['percentage_' . $cat] = 0;
            }
        }

        if (!empty($updateData)) {
            $assessment->update($updateData);
        }
    }
}