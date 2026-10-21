<?php

namespace Sportmonks\SoccerAPI\Tests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Sportmonks\SoccerAPI\Exceptions\ApiRequestException;
use Sportmonks\SoccerAPI\Facades\SoccerAPI;
use Sportmonks\SoccerAPI\Requests\League;

class ClientTest extends TestCase
{
    public function test_requires_a_token(): void
    {
        config(['sportmonks.api_token' => '']);
        $this->expectException(\InvalidArgumentException::class);
        new League();
    }

    public function test_registers_the_facade_and_configuration(): void
    {
        $this->assertInstanceOf(League::class, SoccerAPI::leagues());
        $this->assertSame('https://api.sportmonks.com/v3/', config('sportmonks.api_url'));
        $this->assertSame(30, config('sportmonks.timeout'));
    }

    public function test_uses_v3_authentication_query_parameters_and_secure_options(): void
    {
        config(['sportmonks.timeout' => 8, 'sportmonks.connect_timeout' => 3, 'sportmonks.timezone' => 'Europe/Stockholm']);
        Http::fake(function (Request $request, array $options) {
            $this->assertTrue($options['verify']);
            $this->assertFalse($options['allow_redirects']);
            $this->assertEquals(8, $options['timeout']);
            $this->assertEquals(3, $options['connect_timeout']);

            return Http::response(['data' => []]);
        });

        SoccerAPI::fixtures()->setInclude(['participants', 'scores'])
            ->setFilters(['fixtureLeagues:501,271', 'fixtureStates:1'])
            ->setSelect(['id', 'name'])->setPage(2)->setPerPage(20)->all();

        Http::assertSent(function (Request $request): bool {
            $this->assertSame('GET', $request->method());
            $this->assertSame(['test-token'], $request->header('Authorization'));
            $this->assertSame(['application/json'], $request->header('Accept'));
            $this->assertSame([
                'per_page' => 20,
                'page' => 2,
                'include' => 'participants;scores',
                'filters' => 'fixtureLeagues:501,271;fixtureStates:1',
                'select' => 'id,name',
                'timezone' => 'Europe/Stockholm',
            ], $request->data());
            $this->assertStringNotContainsString('test-token', $request->url());
            $this->assertStringStartsWith('https://api.sportmonks.com/v3/football/fixtures?', $request->url());

            return true;
        });
        Http::assertSentCount(1);
    }

    public function test_preserves_v3_participants_scores_and_metadata(): void
    {
        $body = file_get_contents(__DIR__ . '/fixtures/fixture.json');
        Http::fake(['*' => Http::response($body)]);

        $response = SoccerAPI::fixtures()->setInclude(['participants', 'scores'])->byId(18535517);

        $this->assertSame(18535517, $response->data->id);
        $this->assertSame('home', $response->data->participants[0]->meta->location);
        $this->assertSame(2, $response->data->scores[0]->score->goals);
        $this->assertSame('CURRENT', $response->data->scores[0]->description);
        $this->assertSame(53, $response->data->scores[0]->participant_id);
        $this->assertSame(99, $response->rate_limit->remaining);
        $this->assertSame('UTC', $response->timezone);
        $this->assertFalse(property_exists($response->data, 'localteam_id'));
    }

    public function test_preserves_v3_pagination_without_automatically_following_urls(): void
    {
        Http::fake(['*' => Http::response([
            'data' => [['id' => 501]],
            'pagination' => ['current_page' => 1, 'has_more' => true, 'next_page' => 'https://api.sportmonks.com/v3/football/leagues?page=2'],
            'rate_limit' => ['remaining' => 99, 'resets_in_seconds' => 30, 'requested_entity' => 'League'],
        ])]);
        $response = SoccerAPI::leagues()->all();
        $this->assertSame(501, $response->data[0]->id);
        $this->assertTrue($response->pagination->has_more);
        $this->assertSame(1, $response->pagination->current_page);
        $this->assertSame(30, $response->rate_limit->resets_in_seconds);
        Http::assertSentCount(1);
    }

    public function test_skip_data_preserves_arrays_objects_and_null(): void
    {
        config(['sportmonks.skip_data' => true]);
        Http::fake(['*' => Http::sequence()->push(['data' => [['id' => 1]]])->push(['data' => ['id' => 1]])->push(['data' => null])]);
        $this->assertSame(1, SoccerAPI::leagues()->all()[0]->id);
        $this->assertSame(1, SoccerAPI::leagues()->byId(1)->id);
        $this->assertNull(SoccerAPI::leagues()->all());
    }

    #[DataProvider('invalidBodies')]
    public function test_rejects_invalid_json_and_envelopes(string $body): void
    {
        Http::fake(['*' => Http::response($body)]);
        $this->expectException(ApiRequestException::class);
        SoccerAPI::leagues()->all();
    }

    public static function invalidBodies(): array
    {
        return array_map(fn (string $body): array => [$body], ['', '{', 'null', '[]', '"text"', 'false', '123', '{}', '{"data":false}', '{"error":{}}', '{"message":"Denied"}']);
    }

    #[DataProvider('errorStatuses')]
    public function test_http_errors_preserve_status_without_exposing_credentials(int $status): void
    {
        Http::fake(['*' => Http::response(['message' => 'test-token denied'], $status, ['Retry-After' => '60'])]);
        try {
            SoccerAPI::leagues()->all();
            $this->fail('Expected an API exception.');
        } catch (ApiRequestException $exception) {
            $this->assertSame($status, $exception->getCode());
            $this->assertSame(60, $exception->retryAfter());
            $this->assertStringNotContainsString('test-token', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }
        Http::assertSentCount(1);
    }

    public static function errorStatuses(): array
    {
        return [[301], [401], [403], [404], [422], [429], [500], [503]];
    }

    public function test_uses_v3_rate_limit_reset_when_retry_header_is_absent(): void
    {
        Http::fake(['*' => Http::response(['message' => 'Too Many Attempts.', 'rate_limit' => ['resets_in_seconds' => 1847]], 429)]);
        try {
            SoccerAPI::leagues()->all();
            $this->fail('Expected a rate limit exception.');
        } catch (ApiRequestException $exception) {
            $this->assertSame(429, $exception->getCode());
            $this->assertSame(1847, $exception->retryAfter());
        }
        Http::assertSentCount(1);
    }

    public function test_handles_retry_after_dates_and_non_json_errors(): void
    {
        Http::fake(['*' => Http::response('<html>Service unavailable</html>', 503, ['Retry-After' => gmdate(DATE_RFC7231, time() + 60)])]);
        try {
            SoccerAPI::leagues()->all();
            $this->fail('Expected an API exception.');
        } catch (ApiRequestException $exception) {
            $this->assertSame(503, $exception->getCode());
            $this->assertGreaterThanOrEqual(58, $exception->retryAfter());
            $this->assertLessThanOrEqual(60, $exception->retryAfter());
        }
    }

    public function test_reports_connection_failures_without_chaining_credentials(): void
    {
        Http::fake(['*' => Http::failedConnection('Timeout test-token')]);
        try {
            SoccerAPI::leagues()->all();
            $this->fail('Expected a connection failure.');
        } catch (ApiRequestException $exception) {
            $this->assertSame(0, $exception->getCode());
            $this->assertNull($exception->retryAfter());
            $this->assertNull($exception->getPrevious());
            $this->assertStringNotContainsString('test-token', $exception->getMessage());
        }
    }

    public function test_supports_an_injected_http_factory_and_normalizes_base_urls(): void
    {
        config(['sportmonks.api_url' => 'https://api.sportmonks.com/v3']);
        $http = new Factory();
        $http->preventStrayRequests();
        $http->fake(['https://api.sportmonks.com/v3/football/leagues*' => Http::response(['data' => []])]);
        $this->assertSame([], (new League($http))->all()->data);
        $http->assertSentCount(1);
    }

    #[DataProvider('invalidSettings')]
    public function test_rejects_unsafe_settings(string $key, mixed $value): void
    {
        config(['sportmonks.' . $key => $value]);
        $this->expectException(\InvalidArgumentException::class);
        new League();
    }

    public static function invalidSettings(): array
    {
        return [
            ['api_url', 'http://api.sportmonks.com/v3/'],
            ['api_url', 'https://user:password@api.sportmonks.com/v3/'],
            ['api_url', 'https://api.sportmonks.com/v3/?api_token=secret'],
            ['api_url', 'https://api.sportmonks.com/v3/#fragment'],
            ['timeout', 0], ['connect_timeout', -1], ['timeout', INF],
        ];
    }

    public function test_empty_query_options_are_omitted(): void
    {
        config(['sportmonks.timezone' => null, 'app.timezone' => null]);
        Http::fake(['*' => Http::response(['data' => []])]);
        SoccerAPI::leagues()->setInclude([])->setFilters([])->setSelect([])->all();
        Http::assertSent(fn (Request $request): bool => $request->data() === ['per_page' => 50, 'page' => 1]);
    }

    #[DataProvider('invalidPagination')]
    public function test_rejects_invalid_pagination(string $method, int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SoccerAPI::leagues()->$method($value);
    }

    public static function invalidPagination(): array
    {
        return [['setPage', 0], ['setPage', -1], ['setPerPage', 0], ['setPerPage', 51]];
    }
}
