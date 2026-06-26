<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('VERCEL')) {
            $tmpFramework = '/tmp/framework';
            $tmpViews = $tmpFramework.'/views';
            $tmpCache = $tmpFramework.'/cache';
            $tmpDb = '/tmp/database.sqlite';

            File::ensureDirectoryExists($tmpViews);
            File::ensureDirectoryExists($tmpCache);

            if (! file_exists($tmpDb)) {
                touch($tmpDb);
            }

            config([
                'view.compiled' => $tmpViews,
                'cache.stores.file.path' => $tmpCache,
                'cache.stores.file.lock_path' => $tmpCache,
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $tmpDb,
            ]);

            $this->bootstrapVercelSqlite();
        }
    }

    private function bootstrapVercelSqlite(): void
    {
        try {
            Schema::connection('sqlite')->hasTable('users') || Schema::connection('sqlite')->create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });

            Schema::connection('sqlite')->hasTable('kelola_informasi') || Schema::connection('sqlite')->create('kelola_informasi', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();
            });

            Schema::connection('sqlite')->hasTable('agenda_kalender') || Schema::connection('sqlite')->create('agenda_kalender', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->date('event_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            $adminExists = DB::table('users')->where('username', 'admin')->exists();

            if (! $adminExists) {
                DB::table('users')->insert([
                    'name' => 'Administrator Losari',
                    'username' => 'admin',
                    'password' => Hash::make('admin'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (Throwable) {
            // Avoid breaking request flow when ephemeral DB bootstrap fails.
        }
    }
}
