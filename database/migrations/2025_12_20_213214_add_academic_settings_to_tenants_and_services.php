<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Pengaturan di Tabel Sekolah (Tenants)
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('current_academic_year')->nullable()->after('headmaster_nip'); // Contoh: 2024/2025
            $table->string('current_semester')->nullable()->after('current_academic_year'); // Ganjil/Genap
        });

        // 2. Tambah Kolom Semester di Layanan (Academic Year sudah ada di tahap sebelumnya)
        Schema::table('counseling_services', function (Blueprint $table) {
            if (!Schema::hasColumn('counseling_services', 'semester')) {
                $table->string('semester')->nullable()->after('academic_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['current_academic_year', 'current_semester']);
        });
        Schema::table('counseling_services', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};