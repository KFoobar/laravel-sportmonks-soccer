<?php

namespace Sportmonks\SoccerAPI\Facades;

use Illuminate\Support\Facades\Facade;

class SoccerAPI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'soccerapi';
    }
}
