<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('address')->nullable()->after('name');
            $table->string('headmaster_name')->nullable()->after('address');
            $table->string('headmaster_nip')->nullable()->after('headmaster_name');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['address', 'headmaster_name', 'headmaster_nip']);
        });
    }
};