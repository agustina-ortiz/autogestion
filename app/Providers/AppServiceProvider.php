<?php
namespace App\Providers;

use App\Http\Controllers\Auth\InMaestroUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;  // Agregar esta línea
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
        Schema::defaultStringLength(191);  // Agregar esta línea

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Auth::provider('in_maestro', function ($app, array $config) {
            return new InMaestroUserProvider($app['hash'], $config['model']);
        });
    }
}