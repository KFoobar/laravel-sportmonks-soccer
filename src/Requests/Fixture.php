<?php

namespace Sportmonks\SoccerAPI\Requests;

use DateTimeInterface;
use InvalidArgumentException;
use Sportmonks\SoccerAPI\SoccerAPIClient;
use stdClass;

class Fixture extends SoccerAPIClient
{
    public function all(): stdClass|array|null
    {
        return $this->call('football/fixtures');
    }

    public function betweenDates(DateTimeInterface|string $fromDate, DateTimeInterface|string $toDate): stdClass|array|null
    {
        return $this->call('football/fixtures/between/' . $this->date($fromDate) . '/' . $this->date($toDate));
    }

    public function byDate(DateTimeInterface|string $date): stdClass|array|null
    {
        return $this->call('football/fixtures/date/' . $this->date($date));
    }

    public function byId(int $id): stdClass|array|null
    {
        return $this->call("football/fixtures/{$id}");
    }

    public function byMatchId(int $id): stdClass|array|null
    {
        return $this->byId($id);
    }

    public function headToHead(int $firstTeamId, int $secondTeamId): stdClass|array|null
    {
        return $this->call("football/fixtures/head-to-head/{$firstTeamId}/{$secondTeamId}");
    }

    public function multi(array $ids): stdClass|array|null
    {
        if ($ids === []) {
            throw new InvalidArgumentException('At least one fixture ID is required.');
        }

        foreach ($ids as $id) {
            if (!is_int($id) || $id < 1) {
                throw new InvalidArgumentException('Fixture IDs must be positive integers.');
            }
        }

        return $this->call('football/fixtures/multi/' . implode(',', $ids));
    }
}
