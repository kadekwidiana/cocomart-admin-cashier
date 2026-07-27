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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'grab' => [
        'endpoint' => env('GRAB_ENDPOINT'),
        'client_id' => env('GRAB_CLIENT_ID'),
        'client_secret' => env('GRAB_CLIENT_SECRET'),
        'scope' => env('GRAB_SCOPE', 'grab_express.partner_deliveries'),
        'delivery_path' => env('GRAB_DELIVERY_PATH', 'grab-express-sandbox'),
        'default_service_type' => env('GRAB_SERVICE_TYPE', 'INSTANT'),
        'default_vehicle_type' => env('GRAB_VEHICLE_TYPE', 'BIKE'),
    ],

];
