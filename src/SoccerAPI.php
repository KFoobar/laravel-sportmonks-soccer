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
use Sportmonks\SoccerAPI\Requests\Video;
use Sportmonks\SoccerAPI\Requests\Squad;

class SoccerAPI
{
    /**
     * Returns request class for bookmakers.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Bookmaker
     */
    public function bookmakers()
    {
        return new Bookmaker();
    }

    /**
     * Returns request class for commentaries.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Commentary
     */
    public function commentaries()
    {
        return new Commentary();
    }

    /**
     * Returns request class for leagues.
     *
     * @return \Sportmonks\SoccerAPI\Requests\League
     */
    public function leagues()
    {
        return new League();
    }

    /**
     * Returns request class for head2head.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Head2Head
     */
    public function head2head()
    {
        return new Head2Head();
    }

    /**
     * Returns request class for continents.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Continent
     */
    public function continents()
    {
        return new Continent();
    }

    /**
     * Returns request class for countries.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Country
     */
    public function countries()
    {
        return new Country();
    }

    /**
     * Returns request class for fixtures.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Fixture
     */
    public function fixtures()
    {
        return new Fixture();
    }

    /**
     * Returns request class for tvstations.
     *
     * @return \Sportmonks\SoccerAPI\Requests\TVStation
     */
    public function tvstations()
    {
        return new TVStation();
    }

    /**
     * Returns request class for venues.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Venue
     */
    public function venues()
    {
        return new Venue();
    }

    /**
     * Returns request class for rounds.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Round
     */
    public function rounds()
    {
        return new Round();
    }

    /**
     * Returns request class for livescores.
     *
     * @return \Sportmonks\SoccerAPI\Requests\LiveScore
     */
    public function livescores()
    {
        return new LiveScore();
    }

    /**
     * Returns request class for odds.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Odds
     */
    public function odds()
    {
        return new Odds();
    }

    /**
     * Returns request class for players.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Player
     */
    public function players()
    {
        return new Player();
    }

    /**
     * Returns request class for seasons.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Season
     */
    public function seasons()
    {
        return new Season();
    }

    /**
     * Returns request class for standings.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Standings
     */
    public function standings()
    {
        return new Standings();
    }

    /**
     * Returns request class for teams.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Team
     */
    public function teams()
    {
        return new Team();
    }

    /**
     * Returns request class for topscorers.
     *
     * @return \Sportmonks\SoccerAPI\Requests\TopScorer
     */
    public function topscorers()
    {
        return new TopScorer();
    }

    /**
     * Returns request class for videos.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Video
     */
    public function videos()
    {
        return new Video();
    }

    /**
     * Returns request class for squads.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Squad
     */
    public function squads()
    {
        return new Squad();
    }

    /**
     * Returns request class for markets.
     *
     * @return \Sportmonks\SoccerAPI\Requests\Markets
     */
    public function markets()
    {
        return new Markets();
    }
}
