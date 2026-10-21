<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Continent extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('core/continents');
    }

    public function byId(int $continentId): stdClass|array|null
    {
        return $this->call("core/continents/{$continentId}");
    }
}
