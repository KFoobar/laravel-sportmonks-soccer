<?php

namespace Sportmonks\SoccerAPI;

use Illuminate\Support\ServiceProvider;

class SoccerAPIServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sportmonks.php' => config_path('sportmonks.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sportmonks.php',
            'sportmonks'
        );

        $this->app->singleton('soccerapi', function (): SoccerAPI {
            return new SoccerAPI();
        });
    }
}
