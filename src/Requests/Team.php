<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Team extends SoccerAPIClient
{
    public function allBySeasonId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/teams/seasons/{$seasonId}");
    }

    public function byId(int $teamId): stdClass|array|null
    {
        return $this->call("football/teams/{$teamId}");
    }
}
