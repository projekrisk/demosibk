<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Password boleh null (kosong) untuk menandakan belum diaktivasi
            $table->string('password')->nullable()->after('avatar_url');
            
            // Token FCM untuk Notifikasi ke Android (Opsional, persiapan masa depan)
            $table->string('fcm_token')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['password', 'fcm_token']);
        });
    }
};