<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web', // hoặc 'client' nếu bạn muốn client là mặc định
        'passwords' => 'accounts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Ở đây mình định nghĩa 2 guard:
    | - web (dành cho admin)
    | - client (dành cho người dùng mua hàng)
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'accounts',
        ],

        'client' => [
            'driver' => 'session',
            'provider' => 'accounts',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'accounts',
        ],
        
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'accounts' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'accounts' => [
            'provider' => 'accounts',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];
