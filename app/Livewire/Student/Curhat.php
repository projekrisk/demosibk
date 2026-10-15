<?php

namespace App\Livewire\Student;

use App\Models\StudentMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.student')]
class Curhat extends Component
{
    public $subject = '';
    public $message = '';
    public $is_anonymous = false;
    public $counselor_id = null;

    public function submit()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'is_anonymous' => 'boolean',
            'counselor_id' => 'nullable|exists:users,id'
        ]);

        $student = Auth::guard('student')->user();

        StudentMessage::create([
            'tenant_id' => $student->tenant_id,
            'student_id' => $student->id,
            'counselor_id' => $this->counselor_id,
            'is_anonymous' => $this->is_anonymous,
            'sender_name' => $this->is_anonymous ? 'Siswa (Anonim)' : $student->name,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        session()->flash('success', 'Pesan berhasil dikirim. Guru BK akan segera merespons pesan Anda.');
        
        // Reset Form
        $this->reset(['subject', 'message', 'is_anonymous', 'counselor_id']);
    }

    public function render()
    {
        $student = Auth::guard('student')->user();
        
        // Ambil histori pesan siswa ini
        $messages = StudentMessage::where('student_id', $student->id)
            ->with(['targetCounselor', 'replier'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil daftar guru BK di sekolah siswa tersebut
        $counselors = User::where('role', 'konselor')
            ->whereHas('tenants', function ($query) use ($student) {
                $query->where('tenants.id', $student->tenant_id);
            })
            ->get();

        return view('livewire.student.curhat', [
            'messages' => $messages,
            'counselors' => $counselors
        ]);
    }
}