<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'database' => env('DB_DATABASE', 'erp_db'),
            'username' => env('DB_USERNAME', 'project-access'),
            'password' => env('DB_PASSWORD', '*)74TLLtA5825ym*'),
            'charset' => 'utf8',
            'collation' => 'utf8_persian_ci',
            'prefix' => 'erp_'
        ],
    ],
];
