<?php

return [
    'default' => env('DB_ADAPTER', 'mysql'),

    'drivers' => [

        'mysql' => [
            'adapter'  => 'Mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'dbname'   => env('DB_DATABASE', 'phosphorum'),
            'port'     => env('DB_PORT', 3306),
            'username' => env('DB_USERNAME', 'phosphorum'),
            'password' => env('DB_PASSWORD', 'secret'),
            'charset'  => env('DB_CHARSET', 'utf8'),
            'options'  => [
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],

        'sqlite' => [
            'adapter'  => 'Sqlite',
            'dbname'   => env('DB_DATABASE', app_path('phosphorum.sqlite')),
        ],

        'postgresql' => [
            'adapter'  => 'Postgresql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'dbname'   => env('DB_DATABASE', 'phosphorum'),
            'port'     => env('DB_PORT', 5432),
            'username' => env('DB_USERNAME', 'phosphorum'),
            'password' => env('DB_PASSWORD', 'secret'),
            'schema'   => env('DB_SCHEMA', 'public'),
        ],
    ],
];
