<?php

namespace Sportmonks\SoccerAPI\Tests\Requests;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Sportmonks\SoccerAPI\Requests\Fixture;
use Sportmonks\SoccerAPI\Tests\TestCase;

class EndpointsTest extends TestCase
{
    public function test_all_v1_endpoints_keep_their_paths_and_envelopes(): void
    {
        $cases = [
            ['Bookmaker', 'all', [], 'bookmakers'],
            ['Commentary', 'byMatchId', [42], 'commentaries/fixture/42'],
            ['Continent', 'all', [], 'continents/'],
            ['Continent', 'byId', [42], 'continents/42'],
            ['Country', 'all', [], 'countries'],
            ['Country', 'byId', [42], 'countries/42'],
            ['Fixture', 'betweenDates', ['2026-01-01', '2026-01-01'], 'fixtures/between/2026-01-01/2026-01-01'],
            ['Fixture', 'byDate', ['2026-01-01'], 'fixtures/date/2026-01-01'],
            ['Fixture', 'byId', [42], 'fixtures/42'],
            ['Fixture', 'byMatchId', [42], 'fixtures/42'],
            ['Fixture', 'headToHead', [42, 42], 'head2head/42/42'],
            ['Fixture', 'multi', [[42, 43]], 'fixtures/multi/42,43'],
            ['Head2Head', 'betweenTeams', [42, 42], 'head2head/42/42'],
            ['League', 'all', [], 'leagues'],
            ['League', 'byId', [42], 'leagues/42'],
            ['LiveScore', 'today', [], 'livescores'],
            ['LiveScore', 'now', [], 'livescores/now'],
            ['Markets', 'all', [], 'markets'],
            ['Markets', 'byId', [42], 'markets/42'],
            ['Odds', 'byMatchId', [42], 'odds/fixture/42'],
            ['Odds', 'byMatchAndBookmakerId', [42, 42], 'odds/fixture/42/bookmaker/42'],
            ['Odds', 'inplayByMatchId', [42], 'odds/inplay/fixture/42'],
            ['Player', 'byId', [42], 'players/42'],
            ['Round', 'byId', [42], 'rounds/42'],
            ['Round', 'bySeasonId', [42], 'rounds/season/42'],
            ['Season', 'all', [], 'seasons'],
            ['Season', 'byId', [42], 'seasons/42'],
            ['Squad', 'byTeamAndSeason', [42, 42], 'squad/season/42/team/42'],
            ['Standings', 'bySeasonId', [42], 'standings/season/42'],
            ['Standings', 'liveBySeasonId', [42], 'standings/season/live/42'],
            ['TVStation', 'byMatchId', [42], 'tvstations/fixture/42'],
            ['Team', 'allBySeasonId', [42], 'teams/season/42'],
            ['Team', 'byId', [42], 'teams/42'],
            ['TopScorer', 'bySeasonId', [42], 'topscorers/season/42'],
            ['TopScorer', 'aggregatedBySeasonId', [42], 'topscorers/season/42/aggregated'],
            ['Venue', 'byId', [42], 'venues/42'],
            ['Venue', 'bySeasonId', [42], 'venues/season/42'],
            ['Video', 'all', [], 'highlights/'],
            ['Video', 'byMatchId', [42], 'highlights/fixture/42'],
        ];

        foreach ($cases as [$resource, $method, $arguments, $path]) {
            $class = 'Sportmonks\\SoccerAPI\\Requests\\' . $resource;
            $request = new $class($this->client([new Response(200, [], '{"data":{"id":42}}')]));
            $response = $request->$method(...$arguments);
            $this->assertSame(42, $response->data->id, $resource . '::' . $method);
            $this->assertSame('/api/v2.0/' . $path, end($this->history)['request']->getUri()->getPath());
        }
    }

    public function test_formats_carbon_dates(): void
    {
        $fixtures = new Fixture($this->client([
            new Response(200, [], '{"data":[]}'),
            new Response(200, [], '{"data":[]}'),
        ]));
        $fixtures->byDate(Carbon::parse('2026-01-01'));
        $fixtures->betweenDates(Carbon::parse('2026-01-01'), Carbon::parse('2026-01-02'));
        $this->assertSame('/api/v2.0/fixtures/date/2026-01-01', $this->history[0]['request']->getUri()->getPath());
        $this->assertSame('/api/v2.0/fixtures/between/2026-01-01/2026-01-02', $this->history[1]['request']->getUri()->getPath());
    }
}
