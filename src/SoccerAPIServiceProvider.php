<?php

namespace Sportmonks\SoccerAPI;

use Illuminate\Support\ServiceProvider;
use Sportmonks\SoccerAPI\SoccerAPI;

class SoccerAPIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sportmonks.php' => config_path('sportmonks.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sportmonks.php',
            'sportmonks'
        );

        $this->app->singleton('soccerapi', function () {
            return new SoccerAPI();
        });
    }
}
