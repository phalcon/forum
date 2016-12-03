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

return [
    'site' => [
        'name'        => env('APP_NAME'),
        'url'         => env('APP_URL'),
        'project'     => env('APP_PROJECT'),
        'description' => env('APP_DESCRIPTION'),
        'keywords'    => env('APP_KEYWORDS'),
        'software'    => 'Phosphorum',
        'repo'        => env('APP_REPO'),
        'docs'        => env('APP_DOCS'),
    ],

    'theme' => [
        'use_topics_icon'     => env('THEME_TOPICS_ICON'),
        'inactive_topic_icon' => '/icon/new_none.png',
        'active_topic_icon'   => '/icon/new_some.png',
    ],

    'gravatar' => [
        'default_image' => 'identicon',
        'size'          => 24,
        'rating'        => 'pg',
        'use_https'     => true,
    ],

    'application' => [
        'controllersDir' => app_path('controllers') . DIRECTORY_SEPARATOR,
        'modelsDir'      => app_path('models') . DIRECTORY_SEPARATOR,
        'viewsDir'       => app_path('views') . DIRECTORY_SEPARATOR,
        'libraryDir'     => app_path('library') . DIRECTORY_SEPARATOR,
        'staticBaseUri'  => env('APP_STATIC_URL'),
        'baseUri'        => env('APP_BASE_URI'),
        'debug'          => env('APP_DEBUG'),
    ],

    'volt' => [
        'compiledExt'  => '.php',
        'separator'    => '_',
        'cacheDir'     => cache_path('volt') . DIRECTORY_SEPARATOR,
        'forceCompile' => env('APP_DEBUG', true),
    ],

    'mandrillapp' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'github' => [
        'clientId'     => env('GITHUB_CLIENT_ID'),
        'clientSecret' => env('GITHUB_SECRET'),
        'redirectUri'  => env('GITHUB_REDIRECT_URI'),
    ],

    'dropbox' => [
        'accessToken' => env('DROPBOX_TOKEN'),
        'appSecret'   => env('DROPBOX_SECRET'),
        'prefix'      => env('DROPBOX_PREFIX'),
    ],

    'amazonSns' => [
        'secret' => env('AMAZON_SNS_SECRET')
    ],

    'smtp' => [
        'host'     => env('MAIL_HOST'),
        'port'     => env('MAIL_PORT'),
        'security' => env('MAIL_ENCRYPTION'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
    ],

    'beanstalk' => [
        'enabled' => env('BEANSTALK_ENABLED'),
        'host'    => env('BEANSTALK_HOST'),
    ],

    'elasticsearch' => [
        'index' => env('ELASTIC_INDEX'),
        'hosts' => [
            env('ELASTIC_HOST') .':' . env('ELASTIC_PORT'),
        ],
    ],

    'mail' => [
        'fromName'  => env('MAIL_FROM_NAME'),
        'fromEmail' => env('MAIL_FROM_ADDRESS'),
    ],

    'error' => [
        'logger'    => app_path('logs/error.log'),
        'formatter' => [
            'format' => env('LOGGER_FORMAT', '[%date%][%type%] %message%'),
            'date'   => 'd-M-Y H:i:s',
        ],
        'controller' => 'error',
        'action'     => 'index',
    ],

    'reCaptcha' => [
        'siteKey' => env('RECAPTCHA_KEY'),
        'secret' => env('RECAPTCHA_SECRET'),
    ]
];
