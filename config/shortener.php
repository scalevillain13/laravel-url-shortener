<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Require HTTPS for original URLs
    |--------------------------------------------------------------------------
    */
    'require_https_urls' => env('SHORTENER_REQUIRE_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Redirect rate limit (requests per minute per IP)
    |--------------------------------------------------------------------------
    */
    'redirect_rate_limit' => env('SHORTENER_REDIRECT_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | Skip recording clicks from known bots/crawlers
    |--------------------------------------------------------------------------
    */
    'ignore_bots' => env('SHORTENER_IGNORE_BOTS', true),

    /*
    |--------------------------------------------------------------------------
    | Resolve geolocation for clicks (uses ip-api.com in queue job)
    |--------------------------------------------------------------------------
    */
    'geoip_enabled' => env('SHORTENER_GEOIP_ENABLED', true),

];
