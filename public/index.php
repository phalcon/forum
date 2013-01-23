<?php

error_reporting(E_ALL);

require '../../pretty-exceptions/loader.php';

/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";

$loader = new \Phalcon\Loader();

/**
 * Include the loader
 */
require __DIR__ . "/../app/config/loader.php";

$loader->register();

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
$application = new Phalcon\Mvc\Application();
$application->setDI($di);
echo $application->handle()->getContent();
