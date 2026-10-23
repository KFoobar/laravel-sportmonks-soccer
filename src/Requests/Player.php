<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Player extends SoccerAPIClient
{
    public function byId(int $playerId): stdClass|array|null
    {
        return $this->call("football/players/{$playerId}");
    }
}
