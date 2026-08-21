<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'admin',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],
    ],

    'providers' => [
        'admin' => [
            'driver' => 'admin',
        ],
    ],

    'passwords' => [
        'admin' => [
            'provider' => 'admin',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

];
