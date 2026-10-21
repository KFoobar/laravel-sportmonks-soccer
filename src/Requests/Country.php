<?php

namespace Sportmonks\SoccerAPI\Requests;

use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Country extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('core/countries');
    }

    public function byId(int $countryId): stdClass|array|null
    {
        return $this->call("core/countries/{$countryId}");
    }
}
