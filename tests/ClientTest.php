<?php

namespace Sportmonks\SoccerAPI\Tests;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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
        $this->assertSame('https://soccer.sportmonks.com/api/v2.0/', config('sportmonks.api_url'));
        $this->assertSame(30, config('sportmonks.timeout'));
    }

    public function test_preserves_query_parameters_and_response_metadata(): void
    {
        $league = new League($this->client([new Response(200, [], '{"data":[],"meta":{"pagination":{"total_pages":2}}}')]));
        $response = $league->setInclude(['country', 'season'])->setLeagues([1, 2])->setPage(2)->setPerPage(20)->all();
        parse_str($this->history[0]['request']->getUri()->getQuery(), $query);

        $this->assertSame(['api_token' => 'test-token', 'per_page' => '20', 'page' => '2', 'include' => 'country,season', 'tz' => 'UTC', 'leagues' => '1,2'], $query);
        $this->assertSame(2, $response->meta->pagination->total_pages);
        $this->assertTrue($this->history[0]['options']['verify']);
        $this->assertFalse($this->history[0]['options']['allow_redirects']);
        $this->assertSame(30, $this->history[0]['options']['timeout']);
        $this->assertSame(10, $this->history[0]['options']['connect_timeout']);
    }

    public function test_respects_custom_timeouts_and_timezone(): void
    {
        config(['sportmonks.timeout' => 8, 'sportmonks.connect_timeout' => 3, 'sportmonks.timezone' => 'Europe/Stockholm']);
        (new League($this->client([new Response(200, [], '{"data":[]}')])))->all();
        $this->assertSame(8, $this->history[0]['options']['timeout']);
        $this->assertSame(3, $this->history[0]['options']['connect_timeout']);
        $this->assertStringContainsString('tz=Europe%2FStockholm', $this->history[0]['request']->getUri()->getQuery());
    }

    public function test_skip_data_preserves_lists_objects_and_missing_data(): void
    {
        config(['sportmonks.skip_data' => true]);
        $league = new League($this->client([
            new Response(200, [], '{"data":[{"id":1}]}'),
            new Response(200, [], '{"data":{"id":1}}'),
            new Response(200, [], '{}'),
        ]));
        $this->assertSame(1, $league->all()[0]->id);
        $this->assertSame(1, $league->byId(1)->id);
        $this->assertNull($league->all());
    }

    public function test_rejects_invalid_json_and_non_object_responses(): void
    {
        foreach (['', '{', 'null', '[]', '"text"', 'false', '123'] as $body) {
            try {
                (new League($this->client([new Response(200, [], $body)])))->all();
                $this->fail('Expected an invalid response exception.');
            } catch (ApiRequestException $exception) {
                $this->assertStringContainsString('Sportmonks returned', $exception->getMessage());
            }
        }
    }

    public function test_handles_all_legacy_api_error_shapes_and_redacts_tokens(): void
    {
        foreach (['{"error":"test-token denied"}', '{"error":{"message":"test-token denied","code":401}}', '{"error":{}}', '{"error":[]}', '{"error":null}'] as $body) {
            try {
                (new League($this->client([new Response(200, [], $body)])))->all();
                $this->fail('Expected an API exception.');
            } catch (ApiRequestException $exception) {
                $this->assertStringNotContainsString('test-token', $exception->getMessage());
                $this->assertGreaterThan(0, $exception->getCode());
            }
        }
    }

    public function test_preserves_http_exception_types_without_exposing_request_tokens(): void
    {
        foreach ([401, 403, 404, 429, 500] as $status) {
            try {
                (new League($this->client([new Response($status, ['Retry-After' => '60'], '{"message":"Failure"}')])))->all();
                $this->fail('Expected an HTTP exception.');
            } catch (\GuzzleHttp\Exception\RequestException $exception) {
                $this->assertInstanceOf($status < 500 ? ClientException::class : ServerException::class, $exception);
                $this->assertSame($status, $exception->getResponse()->getStatusCode());
                $this->assertSame('60', $exception->getResponse()->getHeaderLine('Retry-After'));
                $this->assertStringNotContainsString('test-token', (string) $exception->getRequest()->getUri());
                $this->assertStringNotContainsString('test-token', $exception->getMessage());
                $this->assertNull($exception->getPrevious());
            }
        }
        $this->assertCount(5, $this->history);
    }

    public function test_preserves_connection_errors_without_exposing_tokens(): void
    {
        $failure = new ConnectException('Timeout test-token', new Request('GET', 'https://soccer.sportmonks.com/?api_token=test-token'));
        try {
            (new League($this->client([$failure])))->all();
            $this->fail('Expected a connection exception.');
        } catch (ConnectException $exception) {
            $this->assertStringNotContainsString('test-token', $exception->getMessage());
            $this->assertStringNotContainsString('test-token', (string) $exception->getRequest()->getUri());
            $this->assertNull($exception->getPrevious());
        }
    }

    public function test_does_not_follow_redirects(): void
    {
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(302);
        (new League($this->client([new Response(302, ['Location' => 'https://example.com'])])))->all();
    }
}
