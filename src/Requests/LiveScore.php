<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class LiveScore extends SoccerAPIClient
{
    public function today(): stdClass|array|null
    {
        return $this->call('football/livescores');
    }

    public function now(): stdClass|array|null
    {
        return $this->call('football/livescores/inplay');
    }
}
