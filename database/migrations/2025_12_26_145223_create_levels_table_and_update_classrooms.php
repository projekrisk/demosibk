<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Levels
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: 10, 11, 12, Lulus
            $table->timestamps();
        });

        // 2. Update Tabel Classrooms (Ganti kolom 'level' string jadi 'level_id')
        Schema::table('classrooms', function (Blueprint $table) {
            // Hapus kolom lama jika ada (backup data dulu jika production)
            if (Schema::hasColumn('classrooms', 'level')) {
                $table->dropColumn('level');
            }
            // Tambah relasi baru
            $table->foreignId('level_id')->nullable()->after('name')->constrained('levels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
            $table->string('level')->nullable();
        });
        Schema::dropIfExists('levels');
    }
};