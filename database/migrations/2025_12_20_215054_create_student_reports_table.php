<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan pengecekan agar aman
        if (Schema::hasTable('student_reports')) {
            return;
        }

        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            
            // PERBAIKAN: Tambahkan ->nullable() sebelum ->constrained()
            // Agar bisa 'Set Null' saat user konselor dihapus
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete(); 
            
            $table->string('academic_year'); 
            $table->enum('semester', ['Ganjil', 'Genap']);
            
            $table->integer('total_achievements')->default(0);
            $table->integer('total_violations')->default(0);
            $table->integer('total_counseling')->default(0); 
            
            $table->text('personality_development')->nullable(); 
            $table->text('social_development')->nullable();      
            $table->text('academic_development')->nullable();    
            $table->text('general_conclusion')->nullable();      
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};