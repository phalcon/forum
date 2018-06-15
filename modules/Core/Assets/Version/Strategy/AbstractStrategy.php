<?php
declare(strict_types=1);

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

namespace Phosphorum\Core\Assets\Version\Strategy;

use Phalcon\Assets\Collection;
use Phosphorum\Core\Assets\Version\StrategyInterface;

/**
 * Phosphorum\Core\Assets\Version\Strategy\AbstractStrategy
 *
 * @package Phosphorum\Core\Assets\Version\Strategy
 */
abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * Assets collection.
     *
     * @var Collection
     */
    private $collection;

    /**
     * Modify final filename or append a GET parameter.
     *
     * @var bool
     */
    private $modifyFilename = true;

    /**
     * The path where resource files are located.
     *
     * @var string|null
     */
    protected $baseResourcePath;

    /**
     * If the strategy shoul check modification time in each request.
     *
     * @var bool
     */
    private $checkModificationTimeAlways = false;

    /**
     * {@inheritdoc}
     *
     * @param Collection $collection
     */
    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;

        $this->attemptUpdateBaseResourcePath(true);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $modify
     */
    public function modifyFilename(bool $modify): void
    {
        $this->modifyFilename = $modify;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $check
     */
    public function checkModificationTimeAlways(bool $check): void
    {
        $this->checkModificationTimeAlways = $check;
    }

    /**
     * Is the versioning strategy should modify the file name?
     */
    public function shouldModifyFilename(): bool
    {
        return $this->modifyFilename;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function resolve()
    {
        // Is collection not initialized yet?
        if ($this->collection == null) {
            return null;
        }

        /** @var string $filename */
        $filename = $this->collection->getTargetPath();

        if ($filename == null || file_exists($filename) == false) {
            return null;
        }

        if ($this->checkModificationTimeAlways) {
            $this->attemptUpdateBaseResourcePath();

            if ($this->isResourcePathPresent() == false) {
                return null;
            }

            // Is the file is outdated?
            if ($this->gc($filename) == true) {
                return null;
            }
        }

        $fileHash = $this->getHash($filename);

        if ($this->shouldModifyFilename() == false) {
            return $filename . '?' . $fileHash;
        }

        $collectionExp = explode('.', $this->collection->getTargetUri());

        return $collectionExp[0] . '.' . $fileHash . '.' . $collectionExp[1];
    }

    /**
     * Get resource file hash.
     *
     * @param  string $fileName
     *
     * @return string
     */
    abstract protected function getHash(string $fileName): string;

    /**
     * Garbage collector.
     *
     * Checks the resource modification time and compare it with the base
     * directory modification time where resource files are located. If
     * the resource is older than the base directory we'll remove resource
     * so that a new resource must be re-generated.
     *
     * @param  string $filename
     *
     * @return bool
     */
    private function gc(string $filename): bool
    {
        if (filemtime($filename) < filemtime($this->baseResourcePath)) {
            unlink($filename);

            return true;
        }

        return false;
    }

    /**
     * Checks if the resource path present.
     *
     * @return bool
     */
    protected function isResourcePathPresent(): bool
    {
        return empty($this->baseResourcePath) == false;
    }

    /**
     * Tries to update the base path where resource files are located.
     *
     * @param  bool $force
     *
     * @return void
     */
    private function attemptUpdateBaseResourcePath(bool $force = false): void
    {
        // Do not set twice
        if ($this->isResourcePathPresent() == true) {
            if ($force == false) {
                return;
            }

            $this->baseResourcePath = null;
        }

        $this->collection->rewind();

        // Is collection not initialized yet?
        if ($this->collection->valid() == false) {
            return;
        }

        $resource = $this->collection->current();
        $this->setBaseResourcePath($resource->getPath());
    }

    /**
     * Sets the base path where resource files are located.
     *
     * @param  string $path
     *
     * @return void
     */
    protected function setBaseResourcePath(string $path): void
    {
        $path = realpath($path);

        if ($path === false || file_exists($path) == false || is_dir($path) == false) {
            return;
        }

        $this->baseResourcePath = $path;
    }
}
