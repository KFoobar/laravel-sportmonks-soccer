<?php

namespace Sportmonks\SoccerAPI;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use JsonException;
use Sportmonks\SoccerAPI\Exceptions\ApiRequestException;
use stdClass;

class SoccerAPIClient
{
    protected Factory $http;
    protected string $apiToken;
    protected string $baseUrl;
    protected bool $withoutData;
    protected string $include = '';
    protected string $filters = '';
    protected string $select = '';
    protected int $perPage = 50;
    protected int $page = 1;
    protected ?string $timezone;
    protected float $timeout;
    protected float $connectTimeout;

    public function __construct(?Factory $http = null)
    {
        $this->http = $http ?? resolve(Factory::class);
        $this->apiToken = (string) config('sportmonks.api_token');

        if (trim($this->apiToken) === '') {
            throw new InvalidArgumentException('No API token set.');
        }

        $this->baseUrl = rtrim((string) config('sportmonks.api_url'), '/') . '/';
        $url = parse_url($this->baseUrl);

        if (!$url || ($url['scheme'] ?? null) !== 'https' || empty($url['host'])
            || isset($url['user']) || isset($url['pass']) || isset($url['query']) || isset($url['fragment'])) {
            throw new InvalidArgumentException('The Sportmonks API URL must be an HTTPS base URL without credentials, a query or a fragment.');
        }

        $this->timezone = config('sportmonks.timezone') ?: config('app.timezone');
        $this->withoutData = (bool) config('sportmonks.skip_data', false);
        $this->timeout = (float) config('sportmonks.timeout', 30);
        $this->connectTimeout = (float) config('sportmonks.connect_timeout', 10);

        if (!is_finite($this->timeout) || !is_finite($this->connectTimeout)
            || $this->timeout <= 0 || $this->connectTimeout <= 0) {
            throw new InvalidArgumentException('Sportmonks timeouts must be positive, finite numbers.');
        }
    }

    protected function call(string $path): stdClass|array|null
    {
        $query = ['per_page' => $this->perPage, 'page' => $this->page];

        foreach (['include' => $this->include, 'filters' => $this->filters, 'select' => $this->select, 'timezone' => $this->timezone] as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }

        try {
            $response = $this->http->acceptJson()
                ->withHeaders(['Authorization' => $this->apiToken])
                ->withOptions(['verify' => true, 'allow_redirects' => false])
                ->timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->get($this->baseUrl . $path, $query);
        } catch (ConnectionException $exception) {
            throw new ApiRequestException('Unable to connect to Sportmonks.');
        }

        try {
            $body = json_decode($response->body(), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ApiRequestException(
                'Sportmonks returned invalid JSON.',
                $response->status(),
                $this->retryAfter($response),
            );
        }

        if (!$response->successful() || (is_object($body) && property_exists($body, 'error'))) {
            throw new ApiRequestException(
                'Sportmonks request failed (HTTP ' . $response->status() . ').',
                $response->status(),
                $this->retryAfter($response, $body),
            );
        }

        if (!$body instanceof stdClass || !property_exists($body, 'data')
            || (!is_array($body->data) && !$body->data instanceof stdClass && $body->data !== null)) {
            throw new ApiRequestException('Sportmonks returned an invalid response envelope.', $response->status());
        }

        return $this->withoutData ? $body->data : $body;
    }

    protected function retryAfter(Response $response, mixed $body = null): ?int
    {
        $retryAfter = $response->header('Retry-After');

        if ($retryAfter !== '') {
            if (ctype_digit($retryAfter)) {
                return (int) $retryAfter;
            }

            $timestamp = strtotime($retryAfter);

            if ($timestamp !== false) {
                return max(0, $timestamp - time());
            }
        }

        $seconds = $body instanceof stdClass ? ($body->rate_limit->resets_in_seconds ?? null) : null;

        return is_int($seconds) && $seconds >= 0 ? $seconds : null;
    }

    protected function date(DateTimeInterface|string $date): string
    {
        if ($date instanceof DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('Dates must use the Y-m-d format.');
        }

        return $date;
    }

    public function setInclude(array|string $include): static
    {
        $this->include = is_array($include) ? implode(';', $include) : $include;

        return $this;
    }

    public function setFilters(array|string $filters): static
    {
        $this->filters = is_array($filters) ? implode(';', $filters) : $filters;

        return $this;
    }

    public function setSelect(array|string $select): static
    {
        $this->select = is_array($select) ? implode(',', $select) : $select;

        return $this;
    }

    public function setPerPage(int $perPage): static
    {
        if ($perPage < 1 || $perPage > 50) {
            throw new InvalidArgumentException('The page size must be between 1 and 50.');
        }

        $this->perPage = $perPage;

        return $this;
    }

    public function setPage(int $page): static
    {
        if ($page < 1) {
            throw new InvalidArgumentException('The page number must be positive.');
        }

        $this->page = $page;

        return $this;
    }
}
