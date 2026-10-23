<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Odds extends SoccerAPIClient
{
    public function byMatchId(int $matchId): stdClass|array|null
    {
        return $this->call("football/odds/pre-match/fixtures/{$matchId}");
    }

    public function byMatchAndBookmakerId(int $matchId, int $bookmakerId): stdClass|array|null
    {
        return $this->call("football/odds/pre-match/fixtures/{$matchId}/bookmakers/{$bookmakerId}");
    }

    public function inplayByMatchId(int $matchId): stdClass|array|null
    {
        return $this->call("football/odds/inplay/fixtures/{$matchId}");
    }
}
