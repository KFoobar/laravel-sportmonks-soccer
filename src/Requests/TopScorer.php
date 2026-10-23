<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class TopScorer extends SoccerAPIClient
{
    public function bySeasonId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/topscorers/seasons/{$seasonId}");
    }
}
