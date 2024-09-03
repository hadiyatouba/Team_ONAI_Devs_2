<?php

namespace App\Providers;

use App\Services\ArticleServiceImpl;
use Illuminate\Support\ServiceProvider;
use App\Services\ArticleServiceInterface;
use App\Repositories\ArticleRepositoryImpl;
use App\Repositories\ArticleRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ArticleServiceInterface::class, ArticleServiceImpl::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepositoryImpl::class);
    }

    public function boot()
    {
        //
    }
}