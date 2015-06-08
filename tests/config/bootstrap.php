<?php

use \Phalcon\Mvc\Application;

defined('APP_PATH') || define('APP_PATH', realpath('.'));

$config = include __DIR__ . "/config.php";
require APP_PATH . "/app/config/loader.php";

$di = new \Phalcon\DI\FactoryDefault();

require APP_PATH . "/app/config/services.php";
require APP_PATH . "/vendor/autoload.php";

$application = new Application;
$application->setDI($di);

return $application;
