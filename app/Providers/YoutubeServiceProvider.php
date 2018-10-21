<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//use Google_Client;
//use Google_Service_YouTube;
//use Illuminate\Auth\AuthenticationException;
use App\Services\YoutubeService;

class YoutubeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(YoutubeService::class, function ($app) {
            // for synchronous usage
//            $apiKey = env('YOUTUBE_API_KEY', false);
//            if (!$apiKey) {
//                throw new AuthenticationException("Youtube API key not found. Check .env file");
//            }
//
//            $client = new Google_Client();
//            $client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
//            $client->setDeveloperKey($apiKey);
//
//            $youtube = new Google_Service_YouTube($client);
//
//            return $youtube;

            // for async usage, custom curl!
            return new YoutubeService();
        });
    }
}
