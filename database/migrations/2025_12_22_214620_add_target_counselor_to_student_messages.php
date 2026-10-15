<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_messages', function (Blueprint $table) {
            // Kolom untuk menampung Guru BK yang dituju (Boleh null jika ingin kirim umum)
            $table->foreignId('counselor_id')->nullable()->after('student_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_messages', function (Blueprint $table) {
            $table->dropForeign(['counselor_id']);
            $table->dropColumn('counselor_id');
        });
    }
};