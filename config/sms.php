<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default SMS Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default SMS driver that will be used when
    | sending SMS messages. You may set this to any of the connections
    | defined in the "drivers" array below.
    |
    */

    'default' => env('SMS_DRIVER', 'lsim'),

    /*
    |--------------------------------------------------------------------------
    | L-sim Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the L-sim SMS service.
    | All environment variables are required for the service to work properly.
    |
    */

    'lsim' => [
        'login' => env('SMS_LOGIN'),
        'password' => env('SMS_PASSWORD'),
        'sender' => env('SMS_SENDER'),
        'base_url' => env('SMS_BASE_URL', 'https://apps.lsim.az/quicksms/v1/send'),
        'balance_url' => env('SMS_BALANCE_URL', 'https://apps.lsim.az/quicksms/v1/balance'),
        'balance_check_enabled' => env('SMS_CHECK_BALANCE_URL', false),
        'timeout' => env('SMS_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how SMS operations are logged. You can specify which
    | channel to use for SMS-related logs.
    |
    */

    'logging' => [
        'channel' => env('SMS_LOG_CHANNEL', 'single'),
        'level' => env('SMS_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for SMS sending to prevent abuse and
    | manage API quotas effectively.
    |
    */

    'rate_limiting' => [
        'enabled' => env('SMS_RATE_LIMIT_ENABLED', false),
        'max_attempts' => env('SMS_RATE_LIMIT_MAX', 10),
        'decay_minutes' => env('SMS_RATE_LIMIT_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related configurations for SMS operations.
    |
    */

    'security' => [
        'verify_ssl' => env('SMS_VERIFY_SSL', true),
        'allowed_recipients' => env('SMS_ALLOWED_RECIPIENTS'), // Comma-separated list for testing
    ],
];