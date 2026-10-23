# Laravel Sportmonks Soccer API

A Laravel 7–11 wrapper for Sportmonks Soccer API v2. PHP 7.3 or PHP 8 is required, subject to your Laravel version's PHP requirements. This package retains the v2 endpoints and response structures. Access depends on your Sportmonks subscription and continued availability of API v2.

Forked from [kirill-latish/laravel-sportmonks-soccer](https://github.com/kirill-latish/laravel-sportmonks-soccer).

## Installation

```bash
composer require kfoobar/laravel-sportmonks-soccer:^1.0
php artisan vendor:publish --provider="Sportmonks\SoccerAPI\SoccerAPIServiceProvider"
```

The service provider and facade are auto-discovered. Configure your token in `.env`:

```dotenv
SPORTMONKS_API_TOKEN=your-token
SPORTMONKS_TIMEOUT=30
SPORTMONKS_CONNECT_TIMEOUT=10
```

Configuration lives in `config/sportmonks.php`. Timeouts are in seconds. `SPORTMONKS_TIMEZONE` defaults to your application's timezone. TLS certificates are always verified and redirects are not followed. The default API URL is `https://soccer.sportmonks.com/api/v2.0/`; custom URLs must retain the trailing slash.

## Usage

```php
use Sportmonks\SoccerAPI\Facades\SoccerAPI;

$leagues = SoccerAPI::leagues()->all();
$fixture = SoccerAPI::fixtures()->byMatchId(18469093);
$fixture = SoccerAPI::fixtures()->byId(18469093);
$seasons = SoccerAPI::seasons()->setInclude(['league', 'fixtures'])->all();
$fixtures = SoccerAPI::fixtures()->betweenDates('2026-01-01', '2026-01-07');
```

`byId()` is an alias for `fixtures()->byMatchId()`. Existing request methods and comma-separated includes and league filters are preserved.

## Responses and pagination

Responses are decoded objects. Lists return an array at `$response->data`; individual resources generally return an object there. Metadata remains available on the envelope.

```php
$response = SoccerAPI::leagues()->setPage(2)->setPerPage(20)->all();
$leagues = $response->data;
$pagination = $response->meta->pagination ?? null;
```

Set `SPORTMONKS_SKIP_DATA=true` to return only `data` (an array, object or null). This discards metadata. Pages are fetched explicitly; no automatic pagination is performed.

## Errors and rate limits

HTTP 4xx/5xx responses retain Guzzle's `ClientException`/`ServerException` types. Connection and timeout failures throw `ConnectException`. Request URLs attached to these exceptions omit `api_token`, and their messages do not include the request URL. API-level errors and malformed JSON throw `Sportmonks\SoccerAPI\Exceptions\ApiRequestException`.

On HTTP 429, inspect `$exception->getResponse()->getHeaderLine('Retry-After')` and the API's rate-limit metadata. Schedule a later attempt in your application or queue. Requests are not retried automatically, so rate limiting cannot cause long sleeps or repeated calls inside a web request. Do not log raw request or response bodies containing credentials.

## Testing

```bash
composer install
composer test
composer validate --strict
```

The test suite uses Orchestra Testbench and mocked HTTP responses. It needs no API token or network access and covers all request routes, configuration, TLS, timeouts, error handling and response decoding. Tests do not verify the live Sportmonks service or subscription access.

See [Sportmonks documentation](https://docs.sportmonks.com/) for API availability and relation definitions.
