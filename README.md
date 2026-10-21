# Laravel Sportmonks Soccer API

A Laravel 12/13 wrapper for the Sportmonks Football API v3. Requires PHP 8.3 or later and a Sportmonks subscription with access to the requested data. Laravel's HTTP client handles transport, using a compatible Guzzle 7 or 8 release.

Forked from [kirill-latish/laravel-sportmonks-soccer](https://github.com/kirill-latish/laravel-sportmonks-soccer).

## Installation

```bash
composer require kfoobar/laravel-sportmonks-soccer:^2.0
php artisan vendor:publish --provider="Sportmonks\SoccerAPI\SoccerAPIServiceProvider"
```

The service provider and facade are auto-discovered. Configure `.env`:

```dotenv
SPORTMONKS_API_TOKEN=your-token
SPORTMONKS_TIMEOUT=30
SPORTMONKS_CONNECT_TIMEOUT=10
```

Configuration lives in `config/sportmonks.php`. Timeouts are positive values in seconds. `SPORTMONKS_TIMEZONE` defaults to your application's timezone. `SPORTMONKS_SKIP_DATA` defaults to `false`.

The base URL is `https://api.sportmonks.com/v3/`. If `SPORTMONKS_API_URL` is set, it must be an HTTPS base URL without a query, fragment or credentials. The client appends the documented `football/`, `core/` or `odds/` path for each resource. TLS verification is always enabled and redirects are disabled.

The API token is sent as the raw `Authorization` header value, without a `Bearer` prefix, according to [Sportmonks authentication](https://docs.sportmonks.com/v3/welcome/authentication).

## Usage

```php
use Sportmonks\SoccerAPI\Facades\SoccerAPI;

$leagues = SoccerAPI::leagues()->all();
$fixture = SoccerAPI::fixtures()
    ->setInclude(['participants', 'scores'])
    ->byId(18535517);

$fixtures = SoccerAPI::fixtures()
    ->setFilters('fixtureLeagues:501,271')
    ->betweenDates('2026-01-01', '2026-01-07');

$standings = SoccerAPI::standings()->liveByLeagueId(501);
$squad = SoccerAPI::squads()->byTeamAndSeason(53, 19735);
```

Date methods accept `Y-m-d` strings or `DateTimeInterface` instances, including Carbon. IDs are integers. `fixtures()->multi([18535517, 18535518])` retrieves multiple fixtures.

Array includes and filters use semicolons. String values are sent unchanged, so use v3 syntax. `setSelect(['id', 'name'])` selects comma-separated fields. Valid includes and filter names depend on the endpoint; see the [endpoint reference](docs/api-v3.md) and [Sportmonks request options](https://docs.sportmonks.com/v3/api/request-options).

```php
$response = SoccerAPI::fixtures()
    ->setInclude(['participants', 'scores'])
    ->setFilters(['fixtureLeagues:501', 'fixtureStates:1'])
    ->setSelect(['id', 'name'])
    ->all();
```

## Responses and pagination

The package returns decoded objects with the native v3 envelope. It does not convert v3 data to the old v2 structure. `$response->data` is an array for lists, an object for individual resources or null when supplied by the API. Included participants and scores remain native v3 arrays; a participant's home/away designation is in `meta.location`, and current score entries have `description` equal to `CURRENT`.

Pagination uses `$response->pagination->has_more` and `current_page`. The v2 `meta.pagination.total_pages` field is not used. Rate-limit information is at `$response->rate_limit`, and subscription and timezone metadata remain available.

```php
$response = SoccerAPI::leagues()->setPage(1)->setPerPage(50)->all();
$leagues = $response->data;
$hasMore = $response->pagination->has_more ?? false;
```

Every page makes one API request. Pagination is explicit, and `next_page` URLs are never followed automatically. `setPage()` requires a positive integer; `setPerPage()` accepts 1–50.

Set `SPORTMONKS_SKIP_DATA=true` to return only `data`. This discards pagination and rate-limit metadata, so keep it false for paginated work.

## Errors and rate limits

HTTP failures, connection/timeouts, malformed JSON and invalid response envelopes throw `Sportmonks\SoccerAPI\Exceptions\ApiRequestException`. `getCode()` returns the HTTP status, or `0` when no response arrived. Messages omit credentials and raw upstream bodies, and transport exceptions containing request credentials are not chained.

`retryAfter()` returns seconds from `Retry-After` (seconds or HTTP date), falling back to v3 `rate_limit.resets_in_seconds`, or null when unavailable. For HTTP 429, schedule a later attempt in your application or queue. The package does not retry or sleep automatically. Successful responses retain the full `rate_limit` object so callers can manage their per-entity budgets. See [Sportmonks rate limits](https://docs.sportmonks.com/v3/api/rate-limit).

## Upgrading from 1.x

1. Use PHP 8.3+ and Laravel 12 or 13, then require `^2.0`.
2. Update published `sportmonks.api_url` and any `SPORTMONKS_API_URL` override to `https://api.sportmonks.com/v3/`. Remove `/api/v2.0/` and `/football/` from the base URL. Confirm your token has v3 access.
3. Update includes, filters and consumers of response data to the v3 schema.
4. Replace Guzzle exception catches with `ApiRequestException` catches. Custom transport injection now accepts `Illuminate\Http\Client\Factory`; `Http::fake()` works throughout the facade.

| Previous usage | Version 2 usage |
| --- | --- |
| `setInclude('localTeam,visitorTeam')` | `setInclude(['participants'])`; use each participant's `meta.location` |
| `setLeagues([501, 271])` | `setFilters('fixtureLeagues:501,271')` for fixture endpoints; choose the documented filter for other entities |
| `scores.localteam_score` / `scores.visitorteam_score` | `setInclude('scores')`; select `CURRENT` entries by `participant_id` |
| `meta.pagination.total_pages` | `pagination.has_more`, with explicit `setPage()` calls |
| `standings()->liveBySeasonId($seasonId)` | `standings()->liveByLeagueId($leagueId)`; supply a league ID |
| `topscorers()->aggregatedBySeasonId()` | Removed. `bySeasonId()` exposes native v3 results; perform any required aggregation in your application |
| `videos()->all()` / `videos()->byMatchId()` | Removed because no corresponding endpoint is documented in the v3 endpoint catalog |

`fixtures()->byMatchId()` remains an alias for `byId()`. Other retained method names use their v3 routes, including head-to-head, squads, rounds, venues, odds and TV stations. `livescores()->today()` maps to v3 All Livescores; `now()` maps to Inplay Livescores. See [the complete route mapping](docs/api-v3.md).

## Testing

```bash
composer install
composer test
composer validate --strict
```

Tests use Orchestra Testbench, Laravel HTTP fakes and synthetic v3 responses based on documented schemas. Unexpected network requests are blocked. CI covers Laravel 12/13 on PHP 8.3–8.5 and the lowest supported dependency combinations. No token or live API access is needed; these tests do not prove subscription permissions or live-service availability.
