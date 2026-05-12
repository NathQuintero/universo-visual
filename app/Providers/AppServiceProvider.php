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
        // En Render (y cualquier producción detrás de proxy HTTPS) forzamos
        // que todas las URLs generadas por route()/url()/asset() usen https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
