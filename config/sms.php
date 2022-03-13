<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sms Credentials Config
    |--------------------------------------------------------------------------
    |
    | Here you can customize your Sms credentials. But do not forget make this same with your .env file
    |
    */

    'credentials' => [
        'login' => env('SMS_LOGIN', null),
        'password' => env('SMS_PASSWORD', null),
        'sender' => env('SMS_SENDER', null),
        'url' => env('SMS_BASE_URL', null),
    ],

];
