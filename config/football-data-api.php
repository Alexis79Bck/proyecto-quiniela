<?php

return [
    'api_key' => env('FOOTBALL_DATA_API_KEY'),
    'base_url' => env('FOOTBALL_DATA_BASE_URL', 'https://api.football-data.org/v4'),   
    'timeout' => env('FOOTBALL_DATA_API_TIMEOUT', 30),
    'retry_times' => env('FOOTBALL_DATA_RETRY_TIMES', 3),
    'retry_delay' => env('FOOTBALL_DATA_RETRY_DELAY', 1000),
];