<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->string('color')->default('info'); // info, warning, danger
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_announcements');
    }
};