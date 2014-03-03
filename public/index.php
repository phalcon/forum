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

/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";

/**
 * Include the loader
 */
require __DIR__ . "/../app/config/loader.php";

/**
 * Include composer autoloader
 */
require __DIR__ . "/../vendor/autoload.php";

try {

	/**
	 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
	 */
	$di = new \Phalcon\DI\FactoryDefault();

	/**
	 * Include the application services
	 */
	require __DIR__ . "/../app/config/services.php";

	/**
	 * Handle the request
	 */
	$application = new Phalcon\Mvc\Application($di);

	echo $application->handle()->getContent();

} catch (Exception $e) {
	echo 'Sorry, an error has ocurred :(';
}
