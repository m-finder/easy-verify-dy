<?php

namespace Wu\EasyVerifyDy\Providers;

use Wu\EasyVerifyDy\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/verify.php' => config_path('verify.php')
        ], 'config');
    }

    public function register(): void
    {
        $this->app->singleton(Application::class, fn($app) =>  new Application());
    }
}