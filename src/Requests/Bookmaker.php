<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Bookmaker extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('odds/bookmakers');
    }
}
