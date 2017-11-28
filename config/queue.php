<?php

return [
    'default' => env('QUEUE_DRIVER', 'fake'),

    'drivers' => [
        'fake' => [
            'adapter' => 'Fake',
        ],

        'beanstalk' => [
            'adapter' => 'Beanstalk',
            'host'    => env('BEANSTALK_HOST', '127.0.0.1'),
            'port'    => env('BEANSTALK_PORT', 11300),
        ],
    ],
];
