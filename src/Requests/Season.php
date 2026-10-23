<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Season extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('football/seasons');
    }

    public function byId(int $seasonId): stdClass|array|null
    {
        return $this->call("football/seasons/{$seasonId}");
    }
}
