<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Venue extends SoccerAPIClient
{
    public function byId(int $venueId): stdClass|array|null
    {
        return $this->call("football/venues/{$venueId}");
    }

    public function bySeasonId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/venues/seasons/{$seasonId}");
    }
}
