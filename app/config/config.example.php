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
use Phalcon\Logger;

return new Config([
    'site' => [
        'name'        => 'Phalcon Framework',
        'url'         => 'http://pforum.loc',
        'description' => 'Phosphorum - Official Phalcon Forum. Get support using Phalcon, the next-generation PHP Framework.',
        'keywords'    => 'php, phalcon, phalcon php, php framework, faster php framework, forum, phosphorum',
        'project'     => 'Phalcon',
        'software'    => 'Phosphorum',
        'repo'        => 'https://github.com/phalcon/cphalcon/issues',
        'docs'        => 'https://github.com/phalcon/docs',
    ],

    'theme' => [
        'use_topics_icon'     => true,
        'inactive_topic_icon' => '/icon/new_none.png',
        'active_topic_icon'   => '/icon/new_some.png',
    ],

    'gravatar' => [
        'default_image' => 'identicon',
        'size'          => 24,
        'rating'        => 'pg',
        'use_https'     => true,
    ],

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'phosphorum',
        'charset'  => 'utf8'
    ],

    'metadata' => [
        'adapter'     => 'Files',
        'metaDataDir' => BASE_DIR . 'app/cache/metaData/',
    ],

    'application' => [
        'controllersDir' => BASE_DIR . 'app/controllers/',
        'modelsDir'      => BASE_DIR . 'app/models/',
        'viewsDir'       => BASE_DIR . 'app/views/',
        'pluginsDir'     => BASE_DIR . 'app/plugins/',
        'libraryDir'     => BASE_DIR . 'app/library/',
        'development'    => [
            'staticBaseUri' => '/',
            'baseUri'       => '/'
        ],
        'production' => [
            'staticBaseUri' => 'http://static.phosphorum.com/',
            'baseUri'       => '/'
        ],
        'debug' => true
    ],

    'volt' => [
        'compiledExt'  => '.php',
        'separator'    => '_',
        'cacheDir'     => BASE_DIR . 'app/cache/volt/',
        'forceCompile' => true,
    ],

    'dataCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'forum-data-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/data/',
    ],

    'modelsCache' => [
        'backend'  => 'File',
        'frontend' => 'Data',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'forum-models-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/models/',
    ],

    'viewCache' => [
        'backend'  => 'File',
        'lifetime' => 30 * 24 * 60 * 60,
        'prefix'   => 'forum-views-cache-',
        'cacheDir' => BASE_DIR . 'app/cache/views/',
    ],

    'session' => [
        'adapter' => 'Files',
    ],

    'mandrillapp' => [
        'secret' => ''
    ],

    'github' => [
        'clientId'     => '',
        'clientSecret' => '',
        'redirectUri'  => 'http://pforum.loc/login/oauth/access_token/'
    ],

    'amazonSns' => [
        'secret' => ''
    ],

    'smtp' => [
        'host'     => "",
        'port'     => 25,
        'security' => "tls",
        'username' => "",
        'password' => ""
    ],

    'beanstalk' => [
        'disabled' => true,
        'host'     => '127.0.0.1'
    ],

    'elasticsearch' => [
        'index' => 'phosphorum'
    ],

    'mail' => [
        'fromName'  => 'Phalcon',
        'fromEmail' => 'phosphorum@phalconphp.com',
    ],

    'logger' => [
        'path'     => BASE_DIR . 'app/logs/',
        'format'   => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
        'date'     => 'D j H:i:s',
        'logLevel' => Logger::WARNING,
        'filename' => 'application.log',
    ],

    'error' => [
        'logger'    => BASE_DIR . 'app/logs/error.log',
        'formatter' => [
            'format' => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
            'date'   => 'D j H:i:s',
        ],
        'controller' => 'error',
        'action'     => 'index',
    ],
]);
