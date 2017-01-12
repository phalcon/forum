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

    // smtp, sendmail, mail
    'driver' => env('MAIL_DRIVER', 'smtp'),

    'host' => env('MAIL_HOST'),

    'port' => env('MAIL_PORT'),

    'security' => env('MAIL_ENCRYPTION'),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    'from' => [
        'email' => env('MAIL_FROM_ADDRESS'),
        'name'  => env('MAIL_FROM_NAME'),
    ],

    'sendmail' => '/usr/sbin/sendmail -bs',
];
