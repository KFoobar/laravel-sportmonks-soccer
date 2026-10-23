<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class League extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('football/leagues');
    }

    public function byId(int $leagueId): stdClass|array|null
    {
        return $this->call("football/leagues/{$leagueId}");
    }
}
