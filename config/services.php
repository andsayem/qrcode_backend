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

    'recaptcha' => [
        'sitekey' => env('RECAPTCHA_SITEKEY'),
        'secret' => env('RECAPTCHA_SECRET'),
    ],

    // External checks used by admin technician registration (same APIs the Apon app calls)
    'ssforce' => [
        'url' => rtrim(env('SSFORCE_API_URL', 'https://ssforce.ssgbd.com/api'), '/'),
    ],

    'hris' => [
        'validate_mobile_url' => env('HRIS_VALIDATE_MOBILE_URL', 'https://hris.ssgbd.com/backend/public/api/auth/validate-ssg-mobile'),
    ],
];
