<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            // Menghubungkan hasil pengisian ke Paket Soal tertentu
            $table->foreignId('assessment_package_id')->nullable()->after('tenant_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->dropForeign(['assessment_package_id']);
            $table->dropColumn('assessment_package_id');
        });
    }
};