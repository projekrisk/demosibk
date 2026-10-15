<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek jika tabel sudah ada agar tidak error
        if (Schema::hasTable('student_records')) {
            return;
        }

        Schema::create('student_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            
            // Tipe: Prestasi (Positif) atau Pelanggaran (Negatif)
            $table->string('type'); // 'achievement', 'violation'
            
            $table->string('title'); // Contoh: Juara 1 Lomba Voli / Terlambat Sekolah
            $table->text('description')->nullable();
            $table->integer('points')->default(0); // Bobot poin (bisa positif/negatif)
            $table->date('recorded_at')->default(now());
            
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete(); // Siapa yang mencatat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_records');
    }
};