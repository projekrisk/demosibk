<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tenants (Data Sekolah)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('npsn')->unique()->comment('Nomor Pokok Sekolah Nasional');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('subscription_status')->default('trial'); // trial, active, inactive
            $table->timestamps();
        });

        // 2. Tabel Pivot (Penghubung User <-> Sekolah)
        // Disimpan di sini agar langsung dibuat setelah tenants
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_user');
        Schema::dropIfExists('tenants');
    }
};