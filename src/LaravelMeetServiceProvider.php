<?php

namespace LaravelMeet;

use Illuminate\Support\ServiceProvider;
use LaravelMeet\Contracts\OAuthTokenStore;

class LaravelMeetServiceProvider extends ServiceProvider {
    public function register() {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelmeet.php', 'laravelmeet');
        
        $this->app->singleton('laravelmeet', function ($app) {
            return new LaravelMeetManager();
        });
        
        $this->app->bind(OAuthTokenStore::class, DatabaseOauthTokenStore::class);
    }
    
    public function boot() {
        $this->publishes([
            __DIR__.'/../config/laravelmeet.php' => config_path('laravelmeet.php'),
        ], 'laravelmeet-config');
        
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'laravelmeet-config');
    }
}
