<?php

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

namespace Phosphorum\AssetsHash;

use Phalcon\Assets\Collection;
use Phalcon\Logger\Adapter\File;
use Phalcon\Di;
use Phalcon\Logger\AdapterInterface;

/**
 * Phosphorum\AssetsHash\AssetsHashAware
 *
 * Base class hash in title of assets collection
 *
 * @package Phosphorum\AssetsHash
 */
abstract class AssetsHashAware implements AssetsHashInterface
{
    /**
     * Assets collection
     *
     * @var Collection $collection
     */
    protected $collection;

    /**
     * Check every times hash in collection's source files
     *
     * @var bool $checkBaseFiles
     */
    protected $checkBaseFiles = false;

    /**
     * Using collection with hash param in the end of file
     *
     * If $useGetParam = false, then file name `/assets/globalJs.1513781110.js`
     * If $useGetParam = true, then file name `/assets/globalJs.js?1513781110`
     *
     * @var bool $useGetParam
     */
    protected $useGetParam = false;

    /**
     * Path to folder where resource files are.
     *
     * @var string $baseResourceFolder
     */
    protected $baseResourceFolder = '';

    /**
     * Logger
     *
     * @var File $logger
     */
    protected $logger;

    /**
     * @param Collection $collection
     * @param bool $checkBaseFiles
     * @param bool $useGetParam
     */
    public function __construct(Collection $collection, $useGetParam = false, $checkBaseFiles = false)
    {
        $this->collection = $collection;
        $this->checkBaseFiles = $checkBaseFiles;
        $this->useGetParam = $useGetParam;
    }

    /**
     * Set logger
     *
     * @param AdapterInterface $logger
     */
    public function setLogger(AdapterInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set assets collection
     *
     * @param Collection $collection
     */
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Set checking every times hash in collection's source files
     *
     * @param bool $checkBaseFiles
     */
    public function setCheckBaseFiles(bool $checkBaseFiles)
    {
        $this->checkBaseFiles = $checkBaseFiles;
    }

    /**
     * Set should use collection with hash param in the end of file
     *
     * @param bool $useGetParam
     */
    public function setUseGetParam(bool $useGetParam)
    {
        $this->useGetParam = $useGetParam;
    }

    /**
     * Set base folder with resource files which use in collection. Especially, if using subfolders
     *
     * <code>
     * $assetsManaderAware->setResourceFolder('../public/js');
     * </code>
     *
     * @param string $path
     */
    public function setResourceFolder(string $path)
    {
        $path = realpath($path);

        if (is_dir($path)) {
            $this->baseResourceFolder = $path;

            return;
        }

        if ($this->logger instanceof AdapterInterface) {
            $this->logger->notice(
                "Path to resource folder isn't correct. Please check it."
            );
        }
    }

    /**
     * Get File name with hash
     *
     * @return string
     */
    public function getHashedFileName()
    {
        $filename = $this->collection->getTargetPath();

        if (!file_exists($filename)) {
            return '';
        }

        //if need checking update time resource files to generate new collection
        if ($this->checkBaseFiles) {
            if (!$this->setFolderForUpdate()) {
                if ($this->logger instanceof AdapterInterface) {
                    $this->logger->notice(
                        "Application can't define base folder for " . array_shift($resources)->getPath() .
                        " collection. " . "Please specify folder with resource files."
                    );
                }

                return '';
            }

            //check collection creating time and changing time of folder with resources.
            //If collection is older - generate new collection
            if (filemtime($filename) < filemtime($this->baseResourceFolder)) {
                unlink($this->collection->getTargetPath());

                return '';
            }
        }

        //Use hash as get param in file name. /assets/collection.js?123456
        if ($this->useGetParam) {
            return $this->collection->getTargetUri() . '?' . $this->getHash($filename);
        }

        //Use hash in file name. /assets/collection.123456.js
        $collectionExp = explode('.', $this->collection->getTargetUri());
        return $collectionExp[0] . '.' . $this->getHash($filename) . '.' . $collectionExp[1];
    }

    /**
     * Set base folder for resource files which use in collection
     *
     * @return bool
     */
    protected function setFolderForUpdate()
    {
        if (!empty($this->baseResourceFolder)) {
            return true;
        }

        $resources = $this->collection->getResources();
        if (empty($resources)) {
            return false;
        }

        $path = dirname(array_shift($resources)->getPath());
        if (!is_dir($path)) {
            return false;
        }

        $this->baseResourceFolder = $path;

        return true;
    }

    /**
     * Get hash string
     *
     * @param string $fileName
     * @return string
     */
    abstract protected function getHash($fileName);
}
