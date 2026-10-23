<?php

namespace Sportmonks\SoccerAPI;

use Sportmonks\SoccerAPI\Requests\Bookmaker;
use Sportmonks\SoccerAPI\Requests\Markets;
use Sportmonks\SoccerAPI\Requests\Commentary;
use Sportmonks\SoccerAPI\Requests\Fixture;
use Sportmonks\SoccerAPI\Requests\Head2Head;
use Sportmonks\SoccerAPI\Requests\League;
use Sportmonks\SoccerAPI\Requests\Continent;
use Sportmonks\SoccerAPI\Requests\Country;
use Sportmonks\SoccerAPI\Requests\LiveScore;
use Sportmonks\SoccerAPI\Requests\Odds;
use Sportmonks\SoccerAPI\Requests\Player;
use Sportmonks\SoccerAPI\Requests\Round;
use Sportmonks\SoccerAPI\Requests\Season;
use Sportmonks\SoccerAPI\Requests\Standings;
use Sportmonks\SoccerAPI\Requests\Team;
use Sportmonks\SoccerAPI\Requests\TopScorer;
use Sportmonks\SoccerAPI\Requests\TVStation;
use Sportmonks\SoccerAPI\Requests\Venue;
use Sportmonks\SoccerAPI\Requests\Squad;

class SoccerAPI
{
    public function bookmakers(): Bookmaker
    {
        return new Bookmaker();
    }

    public function commentaries(): Commentary
    {
        return new Commentary();
    }

    public function leagues(): League
    {
        return new League();
    }

    public function head2head(): Head2Head
    {
        return new Head2Head();
    }

    public function continents(): Continent
    {
        return new Continent();
    }

    public function countries(): Country
    {
        return new Country();
    }

    public function fixtures(): Fixture
    {
        return new Fixture();
    }

    public function tvstations(): TVStation
    {
        return new TVStation();
    }

    public function venues(): Venue
    {
        return new Venue();
    }

    public function rounds(): Round
    {
        return new Round();
    }

    public function livescores(): LiveScore
    {
        return new LiveScore();
    }

    public function odds(): Odds
    {
        return new Odds();
    }

    public function players(): Player
    {
        return new Player();
    }

    public function seasons(): Season
    {
        return new Season();
    }

    public function standings(): Standings
    {
        return new Standings();
    }

    public function teams(): Team
    {
        return new Team();
    }

    public function topscorers(): TopScorer
    {
        return new TopScorer();
    }

    public function squads(): Squad
    {
        return new Squad();
    }

    public function markets(): Markets
    {
        return new Markets();
    }
}
