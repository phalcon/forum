<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

require 'cli-bootstrap.php';

function clear_cache() {

    $log = new Stream('php://stdout');
    $log->info('Start');

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(BASE_DIR.'/app/cache/'), RecursiveIteratorIterator::CHILD_FIRST
    );

    $excludeDirsNames = [];
    $excludeFileNames = ['.gitignore'];

    foreach($iterator as $entry) {
        if ($entry->isDir()) {
            if (!in_array($entry->getBasename(), $excludeDirsNames)) {
                //echo 'DIR:'.$entry->getPathname().PHP_EOL;
            }
        } elseif (!in_array($entry->getFileName(), $excludeFileNames)) {

            $log->info('Delete:'.$entry->getFileName());
            unlink($entry->getPathname());
        }
    }

    $log->info('End');
}

clear_cache();
