<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            // Ubah student_id jadi nullable karena Bimbingan Klasikal tidak untuk 1 siswa spesifik
            $table->foreignId('student_id')->nullable()->change();
            
            // Tambah kolom target_class untuk menyimpan nama kelas (jika klasikal)
            $table->string('target_class')->nullable()->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable(false)->change();
            $table->dropColumn('target_class');
        });
    }
};