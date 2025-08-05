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
        // Принудительное использование HTTPS в production
        if ($this->app->environment('production') || env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        
        // Устанавливаем правильный корневой URL
        if (env('ASSET_URL')) {
            URL::forceRootUrl(env('ASSET_URL'));
        }
    }
}
