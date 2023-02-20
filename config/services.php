<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'binance' => [
        'ws' => [
            'domain' => env('BINANCE_WS_DOMAIN'),
        ],
        'api' => [
            'domain' => env('BINANCE_API_DOMAIN'),
            'api_key' => env('BINANCE_API_KEY'),
            'api_secret' => env('BINANCE_API_SECRET'),
        ]
    ],

    'supervisor' => [
        'user' => env('SUPERVISOR_USER'),
        'password' => env('SUPERVISOR_PASSWORD'),
    ]
];
