<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return new \Phalcon\Config(array(

    'site' => array(
        'name'      => 'Phalcon Framework',
        'url'       => 'http://pforum.loc',
        'project'   => 'Phalcon',
        'software'  => 'Phosphorum',
        'repo'      => 'https://github.com/phalcon/cphalcon/issues',
        'docs'      => 'https://github.com/phalcon/docs',
    ),

    'database'    => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'forumuser',
        'password' => 'secret',
        'dbname'   => 'forum',
        'charset'  => 'utf8'
    ),

    'application' => array(
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir'      => APP_PATH . '/app/models/',
        'viewsDir'       => APP_PATH . '/app/views/',
        'pluginsDir'     => APP_PATH . '/app/plugins/',
        'libraryDir'     => APP_PATH . '/app/library/',
        'development'    => array(
            'staticBaseUri' => '/',
            'baseUri'       => '/'
        ),
        'production'     => array(
            'staticBaseUri' => 'http://static.phosphorum.com/',
            'baseUri'       => '/'
        ),
        'debug'          => true
    ),

    'mandrillapp' => array(
        'secret' => ''
    ),

    'github'      => array(
        'clientId'     => '',
        'clientSecret' => '',
        'redirectUri'  => 'http://pforum.loc/login/oauth/access_token/'
    ),

    'amazonSns'   => array(
        'secret' => ''
    ),

    'smtp'        => array(
        'host'     => "",
        'port'     => 25,
        'security' => "tls",
        'username' => "",
        'password' => ""
    ),

    'beanstalk'   => array(
        'disabled' => true,
        'host'     => '127.0.0.1'
    ),

    'elasticsearch' => array(
        'index'    => 'phosphorum'
    ),

    'mail'     => array(
        'fromName'     => 'Phalcon',
        'fromEmail'    => 'phosphorum@phalconphp.com',
    )
));
