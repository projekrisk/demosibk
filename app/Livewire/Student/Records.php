<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.student')]
class Records extends Component
{
    public function render()
    {
        $student = Auth::guard('student')->user();
        
        // Ambil rekam jejak prestasi dan pelanggaran siswa ini
        $records = $student->records()->orderBy('recorded_at', 'desc')->get();

        return view('livewire.student.records', [
            'records' => $records
        ]);
    }
}