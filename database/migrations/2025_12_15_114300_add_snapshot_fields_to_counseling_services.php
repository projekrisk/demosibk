<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            // Menyimpan kelas siswa SAAT layanan terjadi (misal: "X-1")
            // Agar saat siswa naik kelas ke "XI-1", data sejarah tetap tertulis "X-1"
            $table->string('student_class')->nullable()->after('student_id');
            
            // Menyimpan Tahun Ajaran (Opsional, atau bisa dihitung dari tanggal)
            $table->string('academic_year')->nullable()->after('student_class');
        });
    }

    public function down(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            $table->dropColumn(['student_class', 'academic_year']);
        });
    }
};