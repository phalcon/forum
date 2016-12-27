<?php

return [
    'default' => env('METADATA_DRIVER', 'memory'),

    'drivers' => [

        'apc' => [
            'adapter' => 'Apc',
        ],

        'memcached' => [
            'adapter' => 'Libmemcached',
            'servers' => [
                [
                    'host'   => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port'   => env('MEMCACHED_PORT', 11211),
                    'weight' => env('MEMCACHED_WEIGHT', 100),
                ]
            ],
        ],

        'file' => [
            'adapter'     => 'Files',
            'metaDataDir' => cache_path('metaData') . DIRECTORY_SEPARATOR,
        ],

        'redis' => [
            'adapter' => 'Redis',
            'host'    => env('REDIS_HOST', '127.0.0.1'),
            'port'    => env('REDIS_PORT', 6379),
            'index'   => env('REDIS_INDEX', 0),
        ],

        'memory' => [
            'adapter' => 'Memory',
        ],

        'session' => [
            'adapter' => 'Session',
        ],
    ],

    'prefix'   => env('METADATA_PREFIX', 'forum_metadata_'),

    'lifetime' => env('METADATA_LIFETIME', 172800),
];
