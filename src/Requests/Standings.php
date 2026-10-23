<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Standings extends SoccerAPIClient
{
    public function bySeasonId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/standings/seasons/{$seasonId}");
    }

    public function liveByLeagueId(int $leagueId): stdClass|array|null
    {
        return $this->call("football/standings/live/leagues/{$leagueId}");
    }
}
