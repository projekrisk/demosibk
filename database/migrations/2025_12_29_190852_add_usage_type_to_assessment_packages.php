<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_packages', function (Blueprint $table) {
            // diagnostic = Angket Kebutuhan (Awal)
            // evaluation = Angket Rapor (Akhir)
            $table->string('usage_type')->default('diagnostic')->after('category'); 
        });
    }

    public function down(): void
    {
        Schema::table('assessment_packages', function (Blueprint $table) {
            $table->dropColumn('usage_type');
        });
    }
};