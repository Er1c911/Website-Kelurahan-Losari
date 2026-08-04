<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potensi_kelurahan_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potensi_kelurahan_item_id')->constrained('potensi_kelurahan_items')->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->longText('image_data')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('potensi_kelurahan_items')) {
            $legacyRows = DB::table('potensi_kelurahan_items')
                ->select(['id', 'image_path', 'image_data'])
                ->where(function ($query) {
                    $query->whereNotNull('image_path')
                        ->orWhereNotNull('image_data');
                })
                ->get();

            $now = now();

            foreach ($legacyRows as $row) {
                DB::table('potensi_kelurahan_images')->insert([
                    'potensi_kelurahan_item_id' => $row->id,
                    'image_path' => $row->image_path,
                    'image_data' => $row->image_data,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('potensi_kelurahan_images');
    }
};
