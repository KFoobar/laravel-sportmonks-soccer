<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Markets extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('odds/markets');
    }

    public function byId(int $marketId): stdClass|array|null
    {
        return $this->call("odds/markets/{$marketId}");
    }
}
