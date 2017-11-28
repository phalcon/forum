<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team and contributors               |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

/**
 * @const APP_START_TIME The start time of the application, used for profiling
 */
define('APP_START_TIME', microtime(true));

/**
 * @const APP_START_MEMORY The memory usage at the start of the application, used for profiling
 */
define('APP_START_MEMORY', memory_get_usage());

// Then register the Composer autoloader
require dirname(__DIR__) . '/vendor/autoload.php';

// Load environment
try {
    (new Dotenv\Dotenv(dirname(app_path())))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    // Skip
}
