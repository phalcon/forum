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
    'path'     => app_path('logs'),

    'format'   => env('LOGGER_FORMAT', '[%date%][%type%] %message%'),

    'date'     => 'd-M-Y H:i:s',

    'level'    => env('LOGGER_LEVEL', 'info'),

    'filename' => env('LOGGER_DEFAULT_FILENAME', 'application'),
];
