<?php

return [
    /*
    |--------------------------------------------------------------------------
    | News API Sources Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for various news API sources
    | including their base URLs, API keys, and default parameters.
    |
    */

    'newsapi' => [
        'name' => 'NewsAPI',
        'base_url' => 'https://newsapi.org/v2/top-headlines',
        'api_key' => env('NEWSAPI_KEY'),
    ],

    'guardian' => [
        'name' => 'The Guardian',
        'base_url' => 'https://content.guardianapis.com/search',
        'api_key' => env('GUARDIAN_API_KEY'),
    ],

    'nytimes' => [
        'name' => 'New York Times',
        'base_url' => 'https://api.nytimes.com/svc/topstories/v2',
        'api_key' => env('NYTIMES_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('NEWS_CACHE_ENABLED', true),
        'ttl' => env('NEWS_CACHE_TTL', 3600), // Cache time in seconds
    ],

    'fetch_interval' => env('NEWS_FETCH_INTERVAL', 30), // minutes

    'retry' => [
        'times' => env('NEWS_API_RETRY_TIMES', 3),
        'sleep' => env('NEWS_API_RETRY_SLEEP', 1000), // milliseconds
    ],
];