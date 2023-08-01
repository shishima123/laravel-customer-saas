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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'product' => env('STRIPE_PRODUCT_ID'),
        'date_retry' => env('STRIPE_DATE_RETRY'),
    ],

    'resource_version' => env('RESOURCE_VERSION'),

    'company' => [
        'name' => env('COMPANY_NAME'),
        'add1' => env('COMPANY_ADD1'),
        'add2' => env('COMPANY_ADD2'),
        'phone' => env('COMPANY_PHONE'),
    ],

    'webhook' => [
        'actions' => [
            'sync_setting' => 'sync_setting',
        ],
        'header' => [
            'x-requested-with' => 'XMLHttpRequest'
        ]
    ]
];
