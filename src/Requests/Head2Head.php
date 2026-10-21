<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Head2Head extends SoccerAPIClient
{
    public function betweenTeams(int $firstTeamId, int $secondTeamId): stdClass|array|null
    {
        return $this->call("football/fixtures/head-to-head/{$firstTeamId}/{$secondTeamId}");
    }
}
