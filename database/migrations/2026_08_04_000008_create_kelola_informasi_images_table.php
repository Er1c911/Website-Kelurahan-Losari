<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelola_informasi_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelola_informasi_id')->constrained('kelola_informasi')->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->longText('image_data')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('kelola_informasi')) {
            $legacyRows = DB::table('kelola_informasi')
                ->select(['id', 'image_path', 'image_data'])
                ->where(function ($query) {
                    $query->whereNotNull('image_path')
                        ->orWhereNotNull('image_data');
                })
                ->get();

            $now = now();

            foreach ($legacyRows as $row) {
                DB::table('kelola_informasi_images')->insert([
                    'kelola_informasi_id' => $row->id,
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
        Schema::dropIfExists('kelola_informasi_images');
    }
};
