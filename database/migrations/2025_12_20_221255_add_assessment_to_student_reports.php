<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            // Menambahkan kolom persentase masalah di tabel Rapor
            $table->integer('assessment_pribadi')->default(0)->after('total_counseling');
            $table->integer('assessment_sosial')->default(0)->after('assessment_pribadi');
            $table->integer('assessment_belajar')->default(0)->after('assessment_sosial');
            $table->integer('assessment_karir')->default(0)->after('assessment_belajar');
        });
    }

    public function down(): void
    {
        Schema::table('student_reports', function (Blueprint $table) {
            $table->dropColumn(['assessment_pribadi', 'assessment_sosial', 'assessment_belajar', 'assessment_karir']);
        });
    }
};