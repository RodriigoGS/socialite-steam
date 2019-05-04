<?php

namespace RodriigoGS\Socialite\Steam;

use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class SteamServiceProvider extends SocialiteServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Socialite::extend('steam', function ($app) {
            $config = $app['config']['services.steam'];
            return Socialite::buildProvider(SteamProvider::class, $config);
        });
    }
}
