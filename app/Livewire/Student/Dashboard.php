<?php

namespace App\Livewire\Student;

use App\Models\AssessmentPackage;
use App\Models\StudentAssessment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.student')]
class Dashboard extends Component
{
    public function render()
    {
        $student = Auth::guard('student')->user();
        $now = now();
        $studentClassroomId = (string) $student->classroom_id;

        // Cari asesmen yang sudah pernah dikerjakan oleh siswa ini
        $completedAssessments = StudentAssessment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->get()
            ->keyBy('assessment_package_id');

        // Cari paket yang aktif dan filter berdasarkan status pengerjaan
        $packages = AssessmentPackage::where('tenant_id', $student->tenant_id)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('deadline_at')->orWhere('deadline_at', '>', $now);
            })
            // FITUR BARU: Filter berdasarkan Target Kelas (JSON Array)
            ->where(function ($query) use ($studentClassroomId) {
                // Kondisi 1: Berlaku untuk semua kelas (target_classrooms kosong)
                $query->whereNull('target_classrooms')
                      ->orWhere('target_classrooms', '[]'); // Cek array string kosong
                
                // Kondisi 2: Jika siswa punya ID kelas, cek apakah terdaftar di array JSON
                if (!empty($studentClassroomId)) {
                    $query->orWhereJsonContains('target_classrooms', $studentClassroomId)
                          ->orWhereJsonContains('target_classrooms', (int)$studentClassroomId);
                }
            })
            ->withCount('questions')
            ->get()
            ->filter(function ($pkg) use ($completedAssessments, $now) {
                if (!$completedAssessments->has($pkg->id)) {
                    $pkg->is_completed = false;
                    return true;
                }
                
                $assessment = $completedAssessments->get($pkg->id);
                if ($assessment->updated_at->isSameDay($now)) {
                    $pkg->is_completed = true;
                    return true;
                }
                
                return false;
            });

        return view('livewire.student.dashboard', [
            'student' => $student,
            'packages' => $packages
        ]);
    }
}