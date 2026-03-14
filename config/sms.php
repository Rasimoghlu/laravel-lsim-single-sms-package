<?php

declare(strict_types=1);

return [
    'login'      => env('LSIM_LOGIN'),
    'password'   => env('LSIM_PASSWORD'),
    'sender'     => env('LSIM_SENDER'),
    'base_url'   => env('LSIM_BASE_URL', 'https://apps.lsim.az/quicksms'),
    'timeout'    => env('LSIM_TIMEOUT', 30),
    'verify_ssl' => env('LSIM_VERIFY_SSL', true),
    'logging'    => [
        'channel' => env('LSIM_LOG_CHANNEL', 'stack'),
        'level'   => env('LSIM_LOG_LEVEL', 'info'),
    ],
];
