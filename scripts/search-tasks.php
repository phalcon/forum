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
        //print_r($search->searchCommon(array('title' => 'ubuntu', 'category' => 6)));
    }
}

try {
    $task = new SearchTasks($config);
    $task->run();
} catch(Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}
