<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_packages', function (Blueprint $table) {
            // Menambahkan kolom batas waktu setelah jadwal rilis
            $table->dateTime('deadline_at')->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_packages', function (Blueprint $table) {
            $table->dropColumn('deadline_at');
        });
    }
};