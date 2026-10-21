<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class TVStation extends SoccerAPIClient
{
    public function byMatchId(int $matchId): stdClass|array|null
    {
        return $this->call("football/tv-stations/fixtures/{$matchId}");
    }
}
