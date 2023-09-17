<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SpotifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('spotify', function ($app) {
            return new \App\Services\SpotifyService();
        });
    }

    public function boot()
    {
        //
    }
}