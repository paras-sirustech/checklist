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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'l2escalation_email' => env('L2ESCALATION_EMAIL', 'suhail@ooredoo.qa'),
    'l3escalation_email' => env('L3ESCALATION_EMAIL', 'Kbenchoubane@ooredoo.qa'),
    'franchisee_manager' => env('FRANCHISEE_MANAGER', 'TSyedMohammad@ooredoo.qa'),
    'retail_head_email' => env('RETAIL_HEAD_EMAIL', 'bspeldewinde@ooredoo.qa'),

];
