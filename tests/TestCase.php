<?php

namespace Sportmonks\SoccerAPI\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sportmonks\SoccerAPI\SoccerAPIServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [SoccerAPIServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('sportmonks.api_token', 'test-token');
        $app['config']->set('app.timezone', 'UTC');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }
}
