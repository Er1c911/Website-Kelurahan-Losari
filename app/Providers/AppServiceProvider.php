<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

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

            File::ensureDirectoryExists($tmpViews);
            File::ensureDirectoryExists($tmpCache);

            config([
                'view.compiled' => $tmpViews,
                'cache.stores.file.path' => $tmpCache,
                'cache.stores.file.lock_path' => $tmpCache,
            ]);
        }
    }
}
