<?php
namespace Sportmonks\SoccerAPI\Tests\Requests;

use Sportmonks\SoccerAPI\Facades\SoccerAPI;
use Sportmonks\SoccerAPI\Tests\TestCase;

class BookmakerTest extends TestCase
{
    /**
     * A basic test.
     *
     * @return void
     */
    public function test_retrieves_all_bookmakers()
    {
        $this->assertEmpty([]);
    }
}
