<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ScanAPI Configuration
    |--------------------------------------------------------------------------
    */

    'version' => env('SCANAPI_VERSION', '2.0'),

    'mode' => env('SCANAPI_MODE', 'LIVE'),

    'merchant_id' => env('SCANAPI_MERCHANT_ID', '4'),

    'api_key' => env('SCANAPI_API_KEY', ''),

    'secret_key' => env('SCANAPI_SECRET_KEY', ''),

    'service_id' => env('SCANAPI_SERVICE_ID', '20000'),

    'service_type' => env('SCANAPI_SERVICE_TYPE', '1'),

    'base_url' => env('SCANAPI_BASE_URL', 'https://mobicardsystems.com/api/v1'),

    'routes' => [
        'prefix' => 'mobicard',
        'middleware' => ['web', 'scanapi.config'],
    ],

    'scan' => [
        'max_retries' => 12,
        'idle_timeout' => 45000, // milliseconds
        'quality_check_interval' => 700, // milliseconds
    ],

    'debug' => env('APP_DEBUG', false),
];
