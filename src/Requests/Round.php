<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Round extends SoccerAPIClient
{
    public function byId(int $roundId): stdClass|array|null
    {
        return $this->call("football/rounds/{$roundId}");
    }

    public function bySeasonId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/rounds/seasons/{$seasonId}");
    }
}
