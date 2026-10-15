<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessment_answers', function (Blueprint $table) {
            // Ubah tipe kolom dari integer ke text
            $table->text('answer_value')->change();
        });
    }

    public function down(): void
    {
        Schema::table('student_assessment_answers', function (Blueprint $table) {
            // Kembalikan ke integer (hati-hati data teks akan hilang/error)
            // $table->integer('answer_value')->change(); 
        });
    }
};