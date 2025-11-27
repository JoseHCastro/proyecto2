<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        
        // Forzar la URL base para producción en subdirectorio
        $appUrl = config('app.url');
        if (str_contains($appUrl, 'tecnoweb.org.bo')) {
            URL::forceRootUrl($appUrl);
            
            // Si usas HTTPS, descomenta la siguiente línea
            // URL::forceScheme('http');
        }
    }
}
