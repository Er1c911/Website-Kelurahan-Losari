<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
            URL::forceScheme('https');

            $tmpFramework = '/tmp/framework';
            $tmpViews = $tmpFramework.'/views';
            $tmpCache = $tmpFramework.'/cache';
            $tmpStoragePublic = '/tmp/storage/app/public';

            File::ensureDirectoryExists($tmpViews);
            File::ensureDirectoryExists($tmpCache);
            File::ensureDirectoryExists($tmpStoragePublic);

            config([
                'view.compiled' => $tmpViews,
                'cache.stores.file.path' => $tmpCache,
                'cache.stores.file.lock_path' => $tmpCache,
                'filesystems.disks.public.root' => $tmpStoragePublic,
                'filesystems.disks.public.url' => '/storage',
                'filesystems.media' => 'public',
            ]);

            try {
                $schemaIncomplete = ! Schema::hasTable('users')
                    || ! Schema::hasTable('kelola_informasi')
                    || ! Schema::hasTable('agenda_kalender')
                    || (Schema::hasTable('kelola_informasi') && ! Schema::hasColumn('kelola_informasi', 'image_data'))
                    || (Schema::hasTable('agenda_kalender')
                        && (! Schema::hasColumn('agenda_kalender', 'start_time') || ! Schema::hasColumn('agenda_kalender', 'end_time')));

                if ($schemaIncomplete) {
                    Artisan::call('migrate', ['--force' => true]);
                }
            } catch (Throwable $exception) {
                Log::error('Automatic migrate check failed on Vercel.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
