<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

use Phalcon\Config;

return new Config([
    'site' => [
        'url' => 'http://localhost:8000/',
    ],
    'application' => [
        'development'    => [
            'staticBaseUri' => 'http://localhost:8000/',
            'baseUri'       => 'http://localhost:8000/'
        ],
        'production' => [
            'staticBaseUri' => 'http://localhost:8000/',
            'baseUri'       => 'http://localhost:8000/'
        ],
        'debug' => true,
    ],
    'volt' => [
        'forceCompile' => true,
    ],
    'metadata' => [
        'adapter' => 'Memory',
    ],
    'dataCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'modelsCache' => [
        'backend'  => 'Memory',
        'frontend' => 'None',
    ],
    'viewCache' => [
        'backend' => 'Memory',
    ],
]);
