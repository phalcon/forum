<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
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
    /** @var array */
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
        $this->outputMessage('Start');

        $this->outputMessage('Clear file cache...');
        $this->clearFileCache();

        $this->outputMessage('Clear models cache...');
        $this->clearCache('modelsCache');

        $this->outputMessage('Clear view cache...');
        $this->clearCache('viewCache');

        $this->outputMessage('Clear annotations cache...');
        $this->clearCache('annotations');

        $this->outputMessage('Clear assets collections files...');
        $this->clearFileAssetsCollection();

        $this->outputMessage('Done');
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    protected function clearCache($service)
    {
        if (!container()->has($service)) {
            return;
        }

        $service = container($service);

        $service->flush();
    }

    /**
     * Delete all assets collections files
     * @return void
     */
    protected function clearFileAssetsCollection()
    {
        foreach ($this->getIterator() as $entry) {
            if ($entry->isDir() || in_array($entry->getBasename(), $this->excludeFileNames)) {
                continue;
            }

            unlink($entry->getPathname());
        }
    }

    /**
     * @return RecursiveIteratorIterator
     */
    protected function getIterator()
    {
        $registry = $this->getDI()->get('registry');

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($registry->offsetGet('paths')->public . 'assets'),
            RecursiveIteratorIterator::CHILD_FIRST
        );
    }
}
