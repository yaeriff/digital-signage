<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Kode kamu yang sudah ada (untuk HTTPS di production)
        if ($this->app->environment('production') || env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // TAMBAHKAN KODE INI DI BAWAHNYA:
        // Menghubungkan folder storage ke public secara otomatis jika belum ada (Fix 404 Railway)
        if (!file_exists(public_path('storage'))) {
            app('files')->link(storage_path('app/public'), public_path('storage'));
        }
    }
}
