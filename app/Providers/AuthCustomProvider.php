<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthentificationServiceInterface;
use App\Services\AuthentificationPassport;

class AuthCustomProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthentificationServiceInterface::class, function ($app) {
            return new AuthentificationPassport();
        });
    }

    public function boot()
    {
        //
    }
}