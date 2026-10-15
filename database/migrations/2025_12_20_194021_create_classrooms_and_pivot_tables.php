<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kelas
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: X-1, XII IPA 5
            $table->string('level')->nullable(); // Contoh: 10, 11, 12
            $table->timestamps();
        });

        // 2. Tabel Pivot (Konselor <-> Kelas)
        Schema::create('classroom_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        // 3. Update Siswa (Tambah classroom_id)
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('tenant_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
        Schema::dropIfExists('classroom_user');
        Schema::dropIfExists('classrooms');
    }
};