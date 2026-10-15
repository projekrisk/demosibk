<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Jurnal Harian Guru
        Schema::create('daily_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Guru yang mengisi
            $table->date('date');
            $table->text('activity');
            $table->integer('duration_minutes')->default(60);
            $table->timestamps();
        });

        // 2. Tabel Kotak Masalah (Pesan Siswa)
        Schema::create('student_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            // student_id Nullable agar mendukung fitur "Anonim"
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('sender_name')->nullable(); // Nama pengirim (backup jika anonim)
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_anonymous')->default(false);
            
            // Fitur Balasan
            $table->text('response')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_messages');
        Schema::dropIfExists('daily_journals');
    }
};