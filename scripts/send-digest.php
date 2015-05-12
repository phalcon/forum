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

/**
 * This script sends a weekly digest to users
 */
require 'cli-bootstrap.php';

use Phosphorum\Mail\Digest;
use Phalcon\DI\Injectable;

class SendDigest extends Injectable
{

    public function run()
    {
        $digest = new Digest();
        $digest->send();
    }
}

try {
    $task = new SendDigest($config);
    $task->run();
} catch (Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}
