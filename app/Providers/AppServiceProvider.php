<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Pastikan ini ter-import

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paksa SELALU HTTPS tanpa syarat condition
        URL::forceScheme('https');
    }
}