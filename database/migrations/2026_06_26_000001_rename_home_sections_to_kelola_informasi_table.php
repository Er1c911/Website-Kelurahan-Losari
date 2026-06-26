<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('home_sections') && !Schema::hasTable('kelola_informasi')) {
            Schema::rename('home_sections', 'kelola_informasi');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kelola_informasi') && !Schema::hasTable('home_sections')) {
            Schema::rename('kelola_informasi', 'home_sections');
        }
    }
};
