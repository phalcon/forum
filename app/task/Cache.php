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

namespace Phosphorum\Task;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Phosphorum\Console\AbstractTask;

/**
 * Phosphorum\Task\Cache
 *
 * @package Phosphorum\Task
 */
class Cache extends AbstractTask
{
    protected $excludeFileNames = [
        '.',
        '..',
        '.gitkeep',
        '.gitignore',
    ];

    /**
     * @Doc("Clearing the application cache")
     */
    public function clear()
    {
        $this->output('Start');

        $this->output('Clear file cache...');
        $this->clearFileCache();

        $this->output('Clear models cache...');
        $this->clearCache('modelsCache');

        $this->output('Clear view cache...');
        $this->clearCache('viewCache');

        $this->output('Clear annotations cache...');
        $this->clearCache('annotations');

        $this->output('Done');
    }

    protected function clearFileCache()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(cache_path()),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $entry) {
            if ($entry->isDir() || in_array($entry->getBasename(), $this->excludeFileNames)) {
                continue;
            }

            unlink($entry->getPathname());
        }
    }

    protected function clearCache($service)
    {
        if (!container()->has($service)) {
            return;
        }

        $service = container($service);

        $service->flush();
    }
}
