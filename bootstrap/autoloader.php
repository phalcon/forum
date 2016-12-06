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

use Phalcon\Loader;

// Load constants
require 'constants.php';

(new Loader())
    ->registerNamespaces([
        'Phosphorum\Model'      => dirname(__DIR__) . '/app/model',
        'Phosphorum\Task'       => dirname(__DIR__) . '/app/task',
        'Phosphorum\Controller' => dirname(__DIR__) . '/app/controller',
        'Phosphorum'            => dirname(__DIR__) . '/app/library',
        'Phosphorum\Provider'   => dirname(__DIR__) . '/app/provider',
        'Phosphorum\Listener'   => dirname(__DIR__) . '/app/listener',
    ])
    ->registerFiles([
        __DIR__ . '/helpers.php',
    ])
    ->register();

// Register The Composer Auto Loader
require BASE_DIR . '/vendor/autoload.php';
