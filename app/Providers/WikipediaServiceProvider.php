<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WikipediaService;

class WikipediaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WikipediaService::class, function ($app) {
            return new WikipediaService();
        });
    }
}
