<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return [
    'default' => env('CACHE_DRIVER', 'file'),

    'views'   => env('VIEW_CACHE_DRIVER', 'views'),

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
            'adapter'  => 'File',
            'cacheDir' => cache_path('data') . DIRECTORY_SEPARATOR
        ],

        'views' => [
            'adapter'  => 'File',
            'cacheDir' => cache_path('views') . DIRECTORY_SEPARATOR
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
    ],

    'prefix' => env('CACHE_PREFIX', 'forum_cache_'),

    'lifetime' => env('CACHE_LIFETIME', 86400),
];
