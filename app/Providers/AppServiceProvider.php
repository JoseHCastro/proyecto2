<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar LoginResponse personalizado
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
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
            
            // Forzar HTTPS para todas las URLs generadas por Laravel
            URL::forceScheme('https');
        }
    }
}
