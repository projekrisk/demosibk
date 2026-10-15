<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Transaksi
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('order_id')->unique(); // ID Unik Order Midtrans
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, expired, failed
            $table->string('snap_token')->nullable();
            $table->string('plan_type')->nullable(); // 1_year, 6_months
            $table->timestamps();
        });

        // 2. Tambah kolom masa aktif di Tenant
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('subscription_ends_at');
        });
    }
};