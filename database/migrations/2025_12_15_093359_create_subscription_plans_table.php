<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabel sudah ada. Jika ada, skip pembuatan tabel.
        if (Schema::hasTable('subscription_plans')) {
            return;
        }

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Paket (ex: Tahunan)
            $table->decimal('price', 15, 0); // Harga
            $table->string('duration_label'); // Label Durasi (ex: "/ tahun")
            
            // KOLOM BARU: Untuk mengatur durasi otomatis
            $table->integer('duration_in_days')->default(30); // 14, 30, 180, 365
            
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->json('features')->nullable(); // List fitur (array)
            $table->boolean('is_featured')->default(false); // Paket unggulan?
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Urutan
            $table->string('button_label')->default('Pilih Paket');
            $table->string('button_url')->default('/app/register');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};