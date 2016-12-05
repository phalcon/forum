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
 * Index all existing documents to elastic search
 */
require 'cli-bootstrap.php';

use Phosphorum\Search\Indexer;
use Phalcon\DI\Injectable;

class SearchTasks extends Injectable
{
    public function run()
    {
        $search = new Indexer();
        $search->indexAll();
    }
}

try {
    $task = new SearchTasks();
    $task->run();
} catch (Exception $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
