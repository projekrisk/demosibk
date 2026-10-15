<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bank Soal / Instrumen
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            // Jika tenant_id NULL = Soal Global (Template dari Super Admin)
            // Jika terisi = Soal khusus buatan sekolah tersebut
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('category'); // Pribadi, Sosial, Belajar, Karir
            $table->text('question_text'); // Contoh: "Saya merasa sulit berkonsentrasi belajar"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Sesi Pengisian Asesmen oleh Siswa (Header)
        Schema::create('student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            
            $table->string('academic_year'); // 2024/2025
            $table->date('filled_at')->default(now());
            
            // Hasil Perhitungan Otomatis (Persentase Masalah)
            // Disimpan agar tidak perlu hitung ulang terus menerus (Snapshot)
            $table->integer('percentage_pribadi')->default(0);
            $table->integer('percentage_sosial')->default(0);
            $table->integer('percentage_belajar')->default(0);
            $table->integer('percentage_karir')->default(0);
            
            $table->text('counselor_notes')->nullable(); // Analisa manual guru
            $table->string('status')->default('draft'); // draft, completed
            $table->timestamps();
        });

        // 3. Detail Jawaban Siswa
        Schema::create('student_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_question_id')->constrained()->cascadeOnDelete();
            
            // Jawaban: 1 (Ya/Setuju/Masalah), 0 (Tidak)
            // Bisa diubah jadi skala 1-4 jika menggunakan model AUM/DCM Skala
            $table->integer('answer_value')->default(0); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assessment_answers');
        Schema::dropIfExists('student_assessments');
        Schema::dropIfExists('assessment_questions');
    }
};