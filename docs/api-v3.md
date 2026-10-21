# API v3 endpoint reference

All routes below use `https://api.sportmonks.com/v3/` and the `GET` method. Each source links to the official endpoint specification. Path placeholders identify API IDs; squad arguments remain team first, season second in the PHP method.

| API route | Official specification |
| --- | --- |
| `football/livescores/inplay` | [GET Inplay Livescores](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/livescores/get-inplay-livescores) |
| `football/livescores` | [GET All Livescores](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/livescores/get-all-livescores) |
| `football/fixtures` | [GET All Fixtures](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-all-fixtures) |
| `football/fixtures/{ID}` | [GET Fixture by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixture-by-id) |
| `football/fixtures/multi/{IDs}` | [GET Fixtures by Multiple IDs](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixtures-by-multiple-ids) |
| `football/fixtures/date/{date}` | [GET Fixtures by Date](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixtures-by-date) |
| `football/fixtures/between/{start_date}/{end_date}` | [GET Fixtures by Date Range](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixtures-by-date-range) |
| `football/fixtures/head-to-head/{team_id_1}/{team_id2}` | [GET Fixtures by Head To Head](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixtures-by-head-to-head) |
| `football/leagues` | [GET All Leagues](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/leagues/get-all-leagues) |
| `football/leagues/{ID}` | [GET League by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/leagues/get-league-by-id) |
| `football/seasons` | [GET All Seasons](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/seasons/get-all-seasons) |
| `football/seasons/{ID}` | [GET Seasons by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/seasons/get-seasons-by-id) |
| `football/rounds/{ID}` | [GET Round by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/rounds/get-round-by-id) |
| `football/rounds/seasons/{ID}` | [GET Rounds by Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/rounds/get-rounds-by-season-id) |
| `football/standings/seasons/{ID}` | [GET Standings by Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/standings/get-standings-by-season-id) |
| `football/standings/live/leagues/{ID}` | [Get Live Standings by League ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/standings/get-live-standings-by-league-id) |
| `football/topscorers/seasons/{ID}` | [GET Topscorers by Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/topscorers/get-topscorers-by-season-id) |
| `football/teams/{ID}` | [GET Team by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/teams/get-team-by-id) |
| `football/teams/seasons/{ID}` | [GET Teams by Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/teams/get-teams-by-season-id) |
| `football/players/{ID}` | [GET Player by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/players/get-player-by-id) |
| `football/squads/seasons/{seasonID}/teams/{teamID}` | [GET Team Squad by Team and Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/team-squads/get-team-squad-by-team-and-season-id) |
| `football/venues/{ID}` | [GET Venue by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/venues/get-venue-by-id) |
| `football/venues/seasons/{ID}` | [GET Venues by Season ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/venues/get-venues-by-season-id) |
| `football/tv-stations/fixtures/{ID}` | [GET TV Stations by Fixture ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/tv-stations/get-tv-stations-by-fixture-id) |
| `football/odds/pre-match/fixtures/{ID}` | [Get Odds by Fixture ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/standard-odds-feed/pre-match-odds/get-odds-by-fixture-id) |
| `football/odds/pre-match/fixtures/{ID}/bookmakers/{ID}` | [GET Odds by Fixture ID and Bookmaker ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/standard-odds-feed/pre-match-odds/get-odds-by-fixture-id-and-bookmaker-id) |
| `football/odds/inplay/fixtures/{ID}` | [GET Inplay Odds by Fixture ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/standard-odds-feed/inplay-odds/get-inplay-odds-by-fixture-id) |
| `odds/markets` | [GET All Markets](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/markets/get-all-markets) |
| `odds/markets/{ID}` | [GET Market by ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/markets/get-market-by-id) |
| `odds/bookmakers` | [GET All Bookmakers](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/bookmakers/get-all-bookmakers) |
| `football/commentaries/fixtures/{ID}` | [GET Commentaries by Fixture ID](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/commentaries/get-commentaries-by-fixture-id) |
| `core/continents` | [GET All Continents](https://docs.sportmonks.com/v3/core-api/endpoints/continents/get-all-continents) |
| `core/continents/{ID}` | [GET Continent by ID](https://docs.sportmonks.com/v3/core-api/endpoints/continents/get-continent-by-id) |
| `core/countries` | [GET All Countries](https://docs.sportmonks.com/v3/core-api/endpoints/countries/get-all-countries) |
| `core/countries/{ID}` | [GET Country by ID](https://docs.sportmonks.com/v3/core-api/endpoints/countries/get-country-by-id) |

## Response contracts

The wrapper returns the API v3 envelope and resources without translating them into v2 shapes. The synthetic fixture in `tests/fixtures/fixture.json` exercises participants, scores and response metadata; its values are test data.

- [Fixture fields](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints/fixtures/get-fixture-by-id)
- [Score relation and current score](https://docs.sportmonks.com/v3/tutorials-and-guides/tutorials/includes/scores)
- [Includes](https://docs.sportmonks.com/v3/api/request-options/includes)
- [Pagination](https://docs.sportmonks.com/v3/tutorials-and-guides/tutorials/introduction/pagination)
- [Timezone](https://docs.sportmonks.com/v3/tutorials-and-guides/tutorials/introduction/set-your-time-zone)
- [Authentication](https://docs.sportmonks.com/v3/welcome/authentication)
- [Rate limits](https://docs.sportmonks.com/v3/api/rate-limit)

## Removed v2 methods

The [v3 endpoint catalog](https://docs.sportmonks.com/v3/endpoints-and-entities/endpoints) does not document a direct replacement for the two video methods or the separate aggregated-topscorers route. These methods are removed instead of constructing undocumented URLs. Season topscorers remain available through the documented season endpoint. Live standings explicitly require a league ID, so the old season-ID method is replaced with `liveByLeagueId()`.
