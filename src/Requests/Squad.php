<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Squad extends SoccerAPIClient
{
    public function byTeamAndSeason(int $teamId, int $seasonId): stdClass|array|null
    {
        return $this->call("football/squads/seasons/{$seasonId}/teams/{$teamId}");
    }
}
