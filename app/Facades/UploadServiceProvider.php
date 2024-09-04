<?php

namespace App\Providers;

use App\Services\UploadService;
use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('upload.service', function ($app) {
            return new UploadService();
        });
    }

    public function boot()
    {
        //
    }
}