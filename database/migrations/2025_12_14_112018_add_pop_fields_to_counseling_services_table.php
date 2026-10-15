<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            // Untuk Klasikal/Kelompok
            $table->string('method')->nullable(); // Diskusi, Ceramah, Sosiodrama
            $table->string('media')->nullable(); // LCD, Video, LKPD
            
            // Untuk Konseling Individu
            $table->string('approach')->nullable(); // Behavioral, Client Centered, dll
            
            // Untuk Kunjungan Rumah / Konferensi
            $table->string('parties_involved')->nullable(); // Orang tua, Wali kelas
            $table->text('location_description')->nullable(); // Deskripsi tempat/rumah
            
            // Evaluasi (Wajib di POP)
            $table->text('process_evaluation')->nullable(); // Evaluasi Proses
        });
    }

    public function down(): void
    {
        Schema::table('counseling_services', function (Blueprint $table) {
            $table->dropColumn(['method', 'media', 'approach', 'parties_involved', 'location_description', 'process_evaluation']);
        });
    }
};