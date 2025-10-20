<?php

namespace App\Providers;

use App\Http\Controllers\Auth\InMaestroUserProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('in_maestro', function ($app, array $config) {
            return new InMaestroUserProvider($app['hash'], $config['model']);
        });
    }
}
