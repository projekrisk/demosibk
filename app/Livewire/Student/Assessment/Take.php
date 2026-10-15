<?php

namespace App\Livewire\Student\Assessment;

use App\Models\AssessmentPackage;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.student')]
class Take extends Component
{
    public $packageId;
    public $packageTitle;
    public $segments = [];
    public $currentSegmentIndex = 0;
    public $answers = [];
    public $assessmentId = null;

    public function mount($package_id)
    {
        $this->packageId = $package_id;
        $student = Auth::guard('student')->user();
        
        $package = AssessmentPackage::where('tenant_id', $student->tenant_id)
            ->where('id', $package_id)
            ->firstOrFail();
            
        if ($package->deadline_at && now()->gt($package->deadline_at)) {
             abort(403, 'Batas waktu pengisian paket ini sudah berakhir.');
        }

        $this->packageTitle = $package->title;

        // Bikin Draft / Resume Draft
        $assessment = StudentAssessment::firstOrCreate(
            [
                'tenant_id' => $student->tenant_id,
                'student_id' => $student->id,
                'assessment_package_id' => $package->id,
                'status' => 'draft'
            ],
            [
                'academic_year' => $student->tenant->current_academic_year ?? (date('Y') . '/' . (date('Y') + 1)),
                'filled_at' => now(),
            ]
        );
        $this->assessmentId = $assessment->id;

        // Load existing answers
        $existingAnswers = StudentAssessmentAnswer::where('student_assessment_id', $assessment->id)->get();
        foreach ($existingAnswers as $ans) {
            $this->answers[$ans->assessment_question_id] = $ans->answer_value;
        }

        // Segmentasi Soal
        $questions = $package->questions()->where('is_active', true)->orderBy('category')->orderBy('sort_order')->get();
        
        $grouped = $questions->groupBy('category');
        foreach ($grouped as $category => $items) {
            $this->segments[] = [
                'category_name' => $category,
                'questions' => $items->toArray()
            ];
        }
    }

    public function updatedAnswers($value, $key)
    {
        if ($this->assessmentId) {
            StudentAssessmentAnswer::updateOrCreate(
                [
                    'student_assessment_id' => $this->assessmentId,
                    'assessment_question_id' => $key
                ],
                [
                    'answer_value' => (string) $value
                ]
            );
        }
    }

    public function nextSegment()
    {
        $currentQuestions = $this->segments[$this->currentSegmentIndex]['questions'];
        $unansweredCount = 0;
        
        $this->resetValidation();
        
        foreach ($currentQuestions as $q) {
            if (!isset($this->answers[$q['id']]) || trim((string) $this->answers[$q['id']]) === '') {
                $this->addError('answers.'.$q['id'], 'Mohon isi jawaban ini.');
                $unansweredCount++;
            }
        }

        if ($unansweredCount > 0) {
            $this->dispatch('validation-failed', ['count' => $unansweredCount]);
            return;
        }

        foreach ($currentQuestions as $q) {
            StudentAssessmentAnswer::updateOrCreate(
                [
                    'student_assessment_id' => $this->assessmentId,
                    'assessment_question_id' => $q['id']
                ],
                [
                    'answer_value' => (string) $this->answers[$q['id']]
                ]
            );
        }

        if ($this->currentSegmentIndex < count($this->segments) - 1) {
            $this->currentSegmentIndex++;
            $this->dispatch('scroll-to-top'); 
        } else {
            $this->finalize();
        }
    }

    public function finalize()
    {
        $assessment = StudentAssessment::with('package')->find($this->assessmentId);
        
        // Hitung ulang semua skor
        $this->calculateScores($assessment);

        $assessment->update(['status' => 'completed']);
        
        session()->flash('success', 'Asesmen berhasil diselesaikan. Terima kasih!');
        return redirect()->route('student.dashboard');
    }

    private function calculateScores($assessment)
    {
        $usageType = $assessment->package->usage_type ?? 'diagnostic';
        $answers = $assessment->answers()->with('question')->get();

        // =======================================================
        // 1. LOGIKA PERHITUNGAN UNTUK RIASEC (PENJURUSAN)
        // =======================================================
        if ($usageType === 'riasec') {
            $riasecScores = ['r' => 0, 'i' => 0, 'a' => 0, 's' => 0, 'e' => 0, 'c' => 0];

            foreach ($answers as $ans) {
                if ($ans->question) {
                    $cat = strtolower(trim($ans->question->category)); // Ambil kategori r, i, a, s, e, c

                    if (array_key_exists($cat, $riasecScores)) {
                        $val = 0;
                        if (is_numeric($ans->answer_value)) {
                            $val = (int) $ans->answer_value;
                        } elseif ($ans->answer_value === 'true' || strtolower($ans->answer_value) === 'ya' || $ans->answer_value === '1') {
                            $val = 1;
                        }
                        $riasecScores[$cat] += $val; // Tambahkan skor
                    }
                }
            }

            // Simpan ke database
            $assessment->update([
                'score_r' => $riasecScores['r'],
                'score_i' => $riasecScores['i'],
                'score_a' => $riasecScores['a'],
                'score_s' => $riasecScores['s'],
                'score_e' => $riasecScores['e'],
                'score_c' => $riasecScores['c'],
            ]);

            return; // Hentikan fungsi sampai di sini agar tidak memproses rumus AKPD
        }

        // =======================================================
        // 2. LOGIKA PERHITUNGAN UNTUK AKPD (MASALAH)
        // =======================================================
        $scores = [
            'pribadi' => ['total' => 0, 'max_score' => 0],
            'sosial'  => ['total' => 0, 'max_score' => 0],
            'belajar' => ['total' => 0, 'max_score' => 0],
            'karir'   => ['total' => 0, 'max_score' => 0],
        ];

        foreach ($answers as $ans) {
            if ($ans->question) {
                $cat = strtolower(trim($ans->question->category)); 
                $type = $ans->question->type;
                
                if (array_key_exists($cat, $scores)) {
                    $val = 0;
                    if (is_numeric($ans->answer_value)) {
                        $val = (int) $ans->answer_value;
                    } elseif ($ans->answer_value === 'true' || strtolower($ans->answer_value) === 'ya' || $ans->answer_value === '1') {
                        $val = 1;
                    }

                    $maxScale = 1; 

                    if ($type === 'likert' || $type === 'scale') {
                        $maxScale = 4;
                    } elseif ($type === 'rating') {
                        $maxScale = 10;
                    }
                    
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
                $field = 'percentage_' . $cat; 
                $percentage = ($val['total'] / $val['max_score']) * 100;
                $percentage = round($percentage); 
                if ($percentage > 100) $percentage = 100;
                $updateData[$field] = $percentage;
            } else {
                 $updateData['percentage_' . $cat] = 0;
            }
        }

        if (!empty($updateData)) {
            $assessment->update($updateData);
        }
    }

    public function render()
    {
        return view('livewire.student.assessment.take');
    }
}