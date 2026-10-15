<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete(); // Wajib untuk SaaS
            $table->string('nisn')->nullable();
            $table->string('name');
            $table->string('class_name'); 
            $table->string('status')->default('Aktif'); // Aktif, Pantauan, dll
            $table->integer('points')->default(100);
            $table->string('avatar_url')->nullable();
            $table->timestamps();

            // Index untuk mempercepat pencarian siswa dalam satu sekolah
            $table->index(['tenant_id', 'nisn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};