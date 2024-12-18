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
        'domain'   => env('MAILGUN_DOMAIN',"alhamwi.agt@gmail.com"),
        'secret'   => env('MAILGUN_SECRET'.""),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id'     => "590924719831-50pb6rvat4p8n45rue9jr675h0m2gjku.apps.googleusercontent.com",
        'client_secret' => "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",
        'redirect'      => "https://www.izocloud.com/auth/google/callback",
    ],

    'stripe' => [
        'secret'     => "sk_test_51NYUJmIasJEHL6yeVNbsHPBVnNlK0tRyA1cEM6ebYW3CCln5c8mHuKnKHIRxPZLODHVqa7QR2qPwwPhf042Vm4aW00TnBVxkgK",   
    ],
    'recaptcha' => [
        'sitekey' => env('NOCAPTCHA_SITEKEY'),
        'secret' => env('NOCAPTCHA_SECRET'),
    ],

];
