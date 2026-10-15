<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\StudentMessage;
use App\Models\Tenant;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

class CurhatPage extends Component
{
    public $npsn;
    public $tenant;

    public $studentSearch = '';
    public $selectedStudentId = null;
    public $selectedStudentName = null;
    
    public $verify_nisn; 
    
    public $subject;
    public $message;
    public $is_anonymous = false;

    public $studentsFound = [];
    public $successMessage = null;

    public function mount($npsn)
    {
        $this->npsn = $npsn;
        $this->tenant = Tenant::where('npsn', $npsn)->firstOrFail();
    }

    public function updatedStudentSearch()
    {
        if ($this->is_anonymous) return;

        if (strlen($this->studentSearch) < 3) {
            $this->studentsFound = [];
            return;
        }

        $this->studentsFound = Student::where('tenant_id', $this->tenant->id)
            ->where('name', 'like', '%' . $this->studentSearch . '%')
            ->limit(5)
            ->get();
    }

    public function selectStudent($id, $name)
    {
        $this->selectedStudentId = $id;
        $this->selectedStudentName = $name;
        $this->studentSearch = '';
        $this->studentsFound = [];
    }

    public function updatedIsAnonymous($value)
    {
        if ($value) {
            $this->reset(['selectedStudentId', 'selectedStudentName', 'studentSearch', 'studentsFound', 'verify_nisn']);
        }
    }

    public function submit()
    {
        $key = 'curhat-submit:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('rate_limit', 'Terlalu banyak percobaan. Silakan tunggu 1 menit.');
            return;
        }
        RateLimiter::hit($key, 60);

        $rules = [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ];

        if (!$this->is_anonymous) {
            $rules['selectedStudentId'] = 'required|exists:students,id';
            $rules['verify_nisn'] = 'required|numeric';
        }

        $this->validate($rules, [
            'selectedStudentId.required' => 'Silakan cari nama Anda atau centang "Kirim Sebagai Anonim".',
            'verify_nisn.required' => 'Masukkan NISN untuk verifikasi identitas.',
            'subject.required' => 'Judul pesan wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
        ]);

        if (!$this->is_anonymous) {
            $student = Student::find($this->selectedStudentId);
            
            if (!$student || $student->nisn !== $this->verify_nisn) {
                $this->addError('verify_nisn', 'NISN tidak cocok dengan nama siswa yang dipilih.');
                return;
            }
        }

        StudentMessage::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->is_anonymous ? null : $this->selectedStudentId,
            'sender_name' => $this->is_anonymous ? 'Seseorang' : null,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_anonymous' => $this->is_anonymous,
        ]);

        $this->successMessage = 'Pesan Anda berhasil dikirim. Privasi Anda aman bersama kami.';
        
        $this->reset(['selectedStudentId', 'selectedStudentName', 'subject', 'message', 'is_anonymous', 'studentSearch', 'verify_nisn']);
    }

    public function render()
    {
        return view('livewire.curhat-page');
    }
}