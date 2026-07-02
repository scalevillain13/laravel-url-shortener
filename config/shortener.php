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
    | API token issuance rate limit (requests per minute per IP)
    |--------------------------------------------------------------------------
    */
    'api_token_rate_limit' => env('SHORTENER_API_TOKEN_RATE_LIMIT', 5),

    /*
    |--------------------------------------------------------------------------
    | Public link creation rate limit (requests per minute per IP)
    |--------------------------------------------------------------------------
    */
    'home_store_rate_limit' => env('SHORTENER_HOME_STORE_RATE_LIMIT', 10),

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
