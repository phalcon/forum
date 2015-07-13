<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2015 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;

defined('APP_PATH') || define('APP_PATH', realpath('.'));

require APP_PATH . "/vendor/autoload.php";

$config = include APP_PATH . "/app/config/config.php";
require APP_PATH . "/app/config/loader.php";

$di = new FactoryDefault;

require APP_PATH . "/app/config/services.php";

$application = new Application;
$application->setDI($di);

return $application;
