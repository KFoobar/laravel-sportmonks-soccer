<?php

namespace Sportmonks\SoccerAPI\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sportmonks\SoccerAPI\SoccerAPIServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected $history = [];

    protected function getPackageProviders($app): array
    {
        return [SoccerAPIServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('sportmonks.api_token', 'test-token');
        $app['config']->set('app.timezone', 'UTC');
    }

    protected function client(array $responses): Client
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($this->history));

        return new Client([
            'base_uri' => config('sportmonks.api_url'),
            'handler' => $handler,
        ]);
    }
}
