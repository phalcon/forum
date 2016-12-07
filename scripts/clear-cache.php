<?php

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
