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

error_reporting(E_ALL);

if (!isset($_GET['_url'])) {
    $_GET['_url'] = '/';
}

define('APP_PATH', realpath('..'));

/**
 * Read the configuration
 */
$config = include APP_PATH . "/app/config/config.php";

/**
 * Include the loader
 */
require APP_PATH . "/app/config/loader.php";

/**
 * Include composer autoloader
 */
require APP_PATH . "/vendor/autoload.php";

try {

    /**
     * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
     */
    $di = new \Phalcon\DI\FactoryDefault();

    /**
     * Include the application services
     */
    require APP_PATH . "/app/config/services.php";

    /**
     * Handle the request
     */
    $application = new Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
    echo 'Sorry, an error has ocurred :(';
}
