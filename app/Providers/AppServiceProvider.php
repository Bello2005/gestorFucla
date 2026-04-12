<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Providers\CustomUserProvider;

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
        // Render (y cualquier proxy HTTPS) termina SSL en el load balancer;
        // el contenedor sólo ve HTTP. Forzar https en producción evita
        // que los assets y URLs se generen con http:// (mixed content).
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Carbon::setLocale('es');

        // Registrar el proveedor de autenticación personalizado
        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        // Habilitar Blade Stack
        \Illuminate\Support\Facades\Blade::withoutDoubleEncoding();
    }
}
