<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Paket (Folder)
        Schema::create('assessment_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            
            $table->string('title'); // Nama Paket (misal: AKPD Kelas X)
            $table->string('category'); // Pribadi, Sosial, Belajar, Karir (Opsional jika ingin per paket)
            $table->text('description')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->dateTime('scheduled_at')->nullable(); // Jadwal rilis (opsional)
            
            $table->timestamps();
        });

        // 2. Update Tabel Soal (Hubungkan ke Paket)
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->foreignId('assessment_package_id')->nullable()->after('tenant_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn('assessment_package_id');
        });
        Schema::dropIfExists('assessment_packages');
    }
};