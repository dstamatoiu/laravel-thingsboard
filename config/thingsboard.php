<?php

return [

    'container' => [
        'namespace' => 'thingsboard',
        'prefix' => [
            'entity' => 'entity',
        ],
    ],

    'rest' => [
        'base_uri' => env('THINGSBOARD_BASE_URI', 'localhost:9090'),
        'authorization' => [
            'header_key' => 'Authorization',
            'token_type' => 'Bearer',
        ],
        'users' => [
            [
                'role' => \JalalLinuX\Thingsboard\Enums\ThingsboardAuthority::SYS_ADMIN(),
                'mail' => env('THINGSBOARD_ADMIN_MAIL', 'sysadmin@thingsboard.org'),
                'pass' => env('THINGSBOARD_ADMIN_PASS', 'sysadmin'),
            ],
            [
                'role' => \JalalLinuX\Thingsboard\Enums\ThingsboardAuthority::TENANT_ADMIN(),
                'mail' => env('THINGSBOARD_TENANT_MAIL', 'tenant@thingsboard.org'),
                'pass' => env('THINGSBOARD_TENANT_PASS', 'tenant'),
            ],
            [
                'role' => \JalalLinuX\Thingsboard\Enums\ThingsboardAuthority::CUSTOMER_USER(),
                'mail' => env('THINGSBOARD_CUSTOMER_MAIL', 'customer@thingsboard.org'),
                'pass' => env('THINGSBOARD_CUSTOMER_PASS', 'customer'),
            ],
        ],
    ],

    'cache' => [
        'prefix' => '_thingsboard_',
        'driver' => env('THINGSBOARD_CACHE_DRIVER', 'redis'),
    ],
];
