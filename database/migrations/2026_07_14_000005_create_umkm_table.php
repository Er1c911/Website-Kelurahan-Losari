<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo_path')->nullable();
            $table->longText('photo_data')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_name');
            $table->string('whatsapp_link');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};
