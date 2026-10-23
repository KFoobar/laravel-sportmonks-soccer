<?php

namespace Sportmonks\SoccerAPI\Tests\Requests;

use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Sportmonks\SoccerAPI\Facades\SoccerAPI;
use Sportmonks\SoccerAPI\Tests\TestCase;

class EndpointsTest extends TestCase
{
    #[DataProvider('endpoints')]
    public function test_uses_documented_v3_endpoints(string $resource, string $method, array $arguments, string $path): void
    {
        Http::fake(['*' => Http::response(['data' => ['id' => 42]])]);
        $response = SoccerAPI::$resource()->$method(...$arguments);

        $this->assertSame(42, $response->data->id);
        Http::assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/v3/' . $path);
        Http::assertSentCount(1);
    }

    public static function endpoints(): array
    {
        return [
            ['bookmakers', 'all', [], 'odds/bookmakers'],
            ['commentaries', 'byMatchId', [42], 'football/commentaries/fixtures/42'],
            ['continents', 'all', [], 'core/continents'],
            ['continents', 'byId', [42], 'core/continents/42'],
            ['countries', 'all', [], 'core/countries'],
            ['countries', 'byId', [42], 'core/countries/42'],
            ['fixtures', 'all', [], 'football/fixtures'],
            ['fixtures', 'betweenDates', ['2026-01-01', '2026-01-02'], 'football/fixtures/between/2026-01-01/2026-01-02'],
            ['fixtures', 'byDate', ['2026-01-01'], 'football/fixtures/date/2026-01-01'],
            ['fixtures', 'byId', [42], 'football/fixtures/42'],
            ['fixtures', 'byMatchId', [42], 'football/fixtures/42'],
            ['fixtures', 'headToHead', [42, 43], 'football/fixtures/head-to-head/42/43'],
            ['fixtures', 'multi', [[42, 43]], 'football/fixtures/multi/42,43'],
            ['head2head', 'betweenTeams', [42, 43], 'football/fixtures/head-to-head/42/43'],
            ['leagues', 'all', [], 'football/leagues'],
            ['leagues', 'byId', [42], 'football/leagues/42'],
            ['livescores', 'today', [], 'football/livescores'],
            ['livescores', 'now', [], 'football/livescores/inplay'],
            ['markets', 'all', [], 'odds/markets'],
            ['markets', 'byId', [42], 'odds/markets/42'],
            ['odds', 'byMatchId', [42], 'football/odds/pre-match/fixtures/42'],
            ['odds', 'byMatchAndBookmakerId', [42, 43], 'football/odds/pre-match/fixtures/42/bookmakers/43'],
            ['odds', 'inplayByMatchId', [42], 'football/odds/inplay/fixtures/42'],
            ['players', 'byId', [42], 'football/players/42'],
            ['rounds', 'byId', [42], 'football/rounds/42'],
            ['rounds', 'bySeasonId', [42], 'football/rounds/seasons/42'],
            ['seasons', 'all', [], 'football/seasons'],
            ['seasons', 'byId', [42], 'football/seasons/42'],
            ['squads', 'byTeamAndSeason', [42, 43], 'football/squads/seasons/43/teams/42'],
            ['standings', 'bySeasonId', [42], 'football/standings/seasons/42'],
            ['standings', 'liveByLeagueId', [43], 'football/standings/live/leagues/43'],
            ['tvstations', 'byMatchId', [42], 'football/tv-stations/fixtures/42'],
            ['teams', 'allBySeasonId', [42], 'football/teams/seasons/42'],
            ['teams', 'byId', [42], 'football/teams/42'],
            ['topscorers', 'bySeasonId', [42], 'football/topscorers/seasons/42'],
            ['venues', 'byId', [42], 'football/venues/42'],
            ['venues', 'bySeasonId', [42], 'football/venues/seasons/42'],
        ];
    }

    public function test_formats_carbon_and_immutable_dates(): void
    {
        Http::fake(['*' => Http::response(['data' => []])]);
        SoccerAPI::fixtures()->byDate(Carbon::parse('2026-01-01'));
        SoccerAPI::fixtures()->betweenDates(new DateTimeImmutable('2026-01-01'), new DateTimeImmutable('2026-01-02'));
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), '/fixtures/date/2026-01-01?'));
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), '/fixtures/between/2026-01-01/2026-01-02?'));
        Http::assertSentCount(2);
    }

    #[DataProvider('invalidDates')]
    public function test_rejects_invalid_dates_before_sending(string $date): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SoccerAPI::fixtures()->byDate($date);
    }

    public static function invalidDates(): array
    {
        return [['2026-02-30'], ['2026-1-1'], ['2026-01-01?api_token=other']];
    }

    #[DataProvider('invalidIds')]
    public function test_rejects_invalid_multi_ids_before_sending(array $ids): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SoccerAPI::fixtures()->multi($ids);
    }

    public static function invalidIds(): array
    {
        return [[[]], [[0]], [[42, '43']], [[42, '../leagues']]];
    }
}
