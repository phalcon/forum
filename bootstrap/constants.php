<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
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
 * @const BASE_DIR The path to the Phosphorum project root
 */
defined('BASE_DIR') || define('BASE_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

/**
 * @const APPLICATION_ENV Current application stage:
 *        production, staging, development, testing
 */
define('APPLICATION_ENV', getenv('APP_ENV') ?: 'development');

/**
 * @const APP_START_TIME The start time of the application, used for profiling
 */
define('APP_START_TIME', microtime(true));

/**
 * @const APP_START_MEMORY The memory usage at the start of the application, used for profiling
 */
define('APP_START_MEMORY', memory_get_usage());

/**
 * @const DEV_IP Developer IP mask
 */
define('DEV_IP', '192.168.');
