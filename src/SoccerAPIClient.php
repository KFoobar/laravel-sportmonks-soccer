<?php

namespace Sportmonks\SoccerAPI;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use JsonException;
use Sportmonks\SoccerAPI\Exceptions\ApiRequestException;

class SoccerAPIClient
{
    protected $client;
    protected $apiToken;
    protected $withoutData;
    protected $include = [];
    protected $leagues = [];
    protected $perPage = 50;
    protected $page = 1;
    protected $timezone;
    
    /**
     * Constructs a new instance.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri'  => config('sportmonks.api_url'),
            'verify'    => true,
        ]);

        $this->apiToken = config('sportmonks.api_token');

        if (empty($this->apiToken)) {
            throw new \InvalidArgumentException('No API token set');
        }

        $this->timezone = !empty(config('sportmonks.timezone'))
            ? config('sportmonks.timezone')
            : config('app.timezone');

        $this->withoutData = !empty(config('sportmonks.skip_data'))
            ? config('sportmonks.skip_data')
            : false;
    }

    /**
     * Builds the query and parses the response.
     *
     * @param  string $url
     * @param  bool   $hasData
     *
     * @throws \Sportmonks\SoccerAPI\Exceptions\ApiRequestException
     *
     * @return mixed
     */
    protected function call(string $url, bool $hasData = false)
    {
        $query = [
            'api_token' => $this->apiToken,
            'per_page'  => $this->perPage,
            'page'      => $this->page
        ];

        if (!empty($this->include)) {
            $query['include'] = $this->include;
        }

        if ($this->timezone) {
            $query['tz'] = $this->timezone;
        }

        if (!empty($this->leagues)) {
            $query['leagues'] = $this->leagues;
        }

        try {
            $response = $this->client->request('GET', $url, [
                'query' => $query,
                'verify' => true,
                'timeout' => config('sportmonks.timeout', 30),
                'connect_timeout' => config('sportmonks.connect_timeout', 10),
                'allow_redirects' => false,
            ]);
        } catch (ConnectException $exception) {
            $request = $exception->getRequest();
            $request = $request->withUri(Uri::withoutQueryValue($request->getUri(), 'api_token'));

            throw new ConnectException('Unable to connect to Sportmonks.', $request);
        } catch (RequestException $exception) {
            $request = $exception->getRequest();
            $request = $request->withUri(Uri::withoutQueryValue($request->getUri(), 'api_token'));
            $exceptionClass = get_class($exception);

            throw new $exceptionClass('Sportmonks HTTP request failed.', $request, $exception->getResponse());
        }

        if ($response->getStatusCode() >= 300) {
            throw new ApiRequestException('Sportmonks returned an unsuccessful response.', $response->getStatusCode());
        }

        try {
            $body = json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ApiRequestException('Sportmonks returned invalid JSON.', 0, $exception);
        }

        if (!is_object($body)) {
            throw new ApiRequestException('Sportmonks returned an invalid response object.');
        }

        if (property_exists($body, 'error')) {
            $error = $body->error;
            $message = is_object($error) ? ($error->message ?? null) : $error;
            $code = is_object($error) && is_numeric($error->code ?? null) ? (int) $error->code : 500;
            $message = is_string($message) ? $message : 'Sportmonks returned an API error.';

            throw new ApiRequestException(str_replace($this->apiToken, '[redacted]', $message), $code);
        }

        if ($this->withoutData) {
            return $body->data ?? null;
        }

        return $body;
    }

    /**
     * Sends the request to the API.
     *
     * @param string $url
     *
     * @return mixed
     */
    protected function callData(string $url)
    {
        return $this->call($url, true);
    }

    /**
     * Sets the include.
     *
     * @param string|array $include
     *
     * @return self
     */
    public function setInclude($include)
    {
        if (is_array($include) && !empty($include)) {
            $include = implode(',', $include);
        }

        $this->include = $include;

        return $this;
    }

    /**
     * Sets the leagues.
     *
     * @param string|array $leagues
     *
     * @return self
     */
    public function setLeagues($leagues)
    {
        if (is_array($leagues) && !empty($leagues)) {
            $leagues = implode(',', $leagues);
        }

        $this->leagues = $leagues;

        return $this;
    }

    /**
     * Sets the per page (limit).
     *
     * @param int $perPage
     *
     * @return self
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Sets the page.
     *
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
    }
}
