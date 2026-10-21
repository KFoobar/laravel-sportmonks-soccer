<?php

return [

    'api_url'   => env('SPORTMONKS_API_URL', 'https://soccer.sportmonks.com/api/v2.0/'),
    'api_token' => env('SPORTMONKS_API_TOKEN'),
    'skip_data' => env('SPORTMONKS_SKIP_DATA', false),
    'timeout' => env('SPORTMONKS_TIMEOUT', 30),
    'connect_timeout' => env('SPORTMONKS_CONNECT_TIMEOUT', 10),
    'timezone'  => env('SPORTMONKS_TIMEZONE'),

];
