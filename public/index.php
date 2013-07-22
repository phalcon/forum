<?php

error_reporting(E_ALL);

//(new Phalcon\Debug)->listen();

/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";

/**
 * Include the loader
 */
require __DIR__ . "/../app/config/loader.php";

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

} catch (Exception $e) { echo 'Sorry, an error has ocurred :('; }
