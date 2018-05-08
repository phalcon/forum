<?php

return [
    'default' => env('QUEUE_DRIVER', 'fake'),

    'drivers' => [
        'fake' => [
            'adapter' => 'Fake',
        ],

        'SQS' => [
            'region'  => 'us-west-2',
            'version' => 'latest',
            'bucket'  => env('AWS_BUCKET', 'default'),
        ],
    ],
];
