<?php

namespace App\Livewire\Student\Auth;

use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.student')]
class Login extends Component
{
    public $npsn = '';
    public $nis = '';
    public $nisn = '';
    public $password = '';
    public $password_confirmation = '';
    
    public $step = 1; 
    public $studentName = '';
    public $studentId = null;

    public function submit()
    {
        if ($this->step === 1) {
            $this->cekData();
        } elseif ($this->step === 2) {
            $this->login();
        } elseif ($this->step === 3) {
            $this->aktivasi();
        }
    }

    private function cekData()
    {
        $this->validate([
            'npsn' => 'required',
            'nis' => 'required',
            'nisn' => 'required',
        ], [
            'npsn.required' => 'NPSN Sekolah wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nisn.required' => 'NISN wajib diisi.',
        ]);

        $tenant = Tenant::where('npsn', $this->npsn)->first();
        if (!$tenant) {
            throw ValidationException::withMessages(['npsn' => 'Sekolah dengan NPSN tersebut tidak terdaftar.']);
        }

        $student = Student::where('tenant_id', $tenant->id)
            ->where('nis', $this->nis)
            ->where('nisn', $this->nisn)
            ->first();

        if (!$student) {
            throw ValidationException::withMessages(['nisn' => 'Data siswa tidak ditemukan. Periksa kembali input Anda.']);
        }

        $this->studentName = $student->name;
        $this->studentId = $student->id;

        if (empty($student->password)) {
            $this->step = 3; 
        } else {
            $this->step = 2; 
        }
    }

    private function login()
    {
        $this->validate(['password' => 'required'], ['password.required' => 'Kata sandi wajib diisi.']);

        $student = Student::find($this->studentId);

        if ($student && Hash::check($this->password, $student->password)) {
            // PERBAIKAN: Menambahkan parameter `true` agar sesi login tersimpan (Remember Me)
            Auth::guard('student')->login($student, true);
            
            $this->redirect(route('student.dashboard'));
        } else {
            throw ValidationException::withMessages(['password' => 'Kata sandi salah.']);
        }
    }

    private function aktivasi()
    {
        $this->validate([
            'password' => 'required|min:6|confirmed'
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        $student = Student::find($this->studentId);
        
        $student->forceFill([
            'password' => $this->password
        ])->save();

        // PERBAIKAN: Menambahkan parameter `true` agar sesi login tersimpan (Remember Me)
        Auth::guard('student')->login($student, true);
        
        $this->redirect(route('student.dashboard'));
    }

    public function resetStep()
    {
        $this->step = 1;
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.student.auth.login');
    }
}