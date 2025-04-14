<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\FirebaseGuard;
class FirebaseAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::extend('firebase', function ($app, $name, array $config) {
            return new FirebaseGuard(
                $app->make('App\Services\FirebaseService'),
                $app->make('request'),
                $config['input_key'] ?? 'token'
            );
        });
    }
}
