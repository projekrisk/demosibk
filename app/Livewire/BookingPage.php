<?php

namespace App\Livewire;

use App\Models\CounselingService;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;

class BookingPage extends Component
{
    public $npsn;
    public $tenant;
    
    public $studentSearch = '';
    public $selectedStudentId = null;
    public $selectedStudentName = null;
    public $counselorId;
    public $date;
    public $time;
    public $message;

    public $successMessage = null;
    public $bookingCode = null;
    public $studentsFound = [];

    public function mount($npsn)
    {
        $this->npsn = $npsn;
        $this->tenant = Tenant::where('npsn', $npsn)->firstOrFail();
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->studentSearch) < 2) {
            $this->studentsFound = [];
            return;
        }

        $this->studentsFound = Student::where('tenant_id', $this->tenant->id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('nisn', 'like', '%' . $this->studentSearch . '%');
            })
            ->limit(5)
            ->get();
    }

    public function selectStudent($id, $name, $nisn)
    {
        $this->selectedStudentId = $id;
        $this->selectedStudentName = "$name ($nisn)";
        $this->studentSearch = '';
        $this->studentsFound = [];
    }

    public function submit()
    {
        $this->validate([
            'selectedStudentId' => 'required|exists:students,id',
            'counselorId' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'message' => 'required|string|max:500',
        ], [
            'selectedStudentId.required' => 'Wajib mencari dan memilih nama siswa.',
            'counselorId.required' => 'Pilih Guru BK yang dituju.',
            'date.required' => 'Tanggal wajib diisi.',
            'time.required' => 'Jam wajib diisi.',
            'message.required' => 'Topik/Pesan wajib diisi.',
        ]);

        $occurredAt = $this->date . ' ' . $this->time;

        $code = 'BK-' . date('Y') . '-' . strtoupper(Str::random(4));

        CounselingService::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->selectedStudentId,
            'counselor_id' => $this->counselorId,
            'service_type' => 'Konseling Individu',
            'status' => 'pending',
            'occurred_at' => $occurredAt,
            'topic' => $this->message,
            'description' => "Booking Online Code: $code",
        ]);

        $this->bookingCode = $code;
        $this->successMessage = 'Permintaan jadwal berhasil dikirim! Guru BK akan meninjau jadwal Anda.';
        
        $this->reset(['selectedStudentId', 'selectedStudentName', 'counselorId', 'date', 'time', 'message']);
    }

    public function render()
    {
        $counselors = $this->tenant->members()->where('role', '!=', 'super_admin')->get();

        return view('livewire.booking-page', [
            'counselors' => $counselors
        ]);
    }
}