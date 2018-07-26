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

namespace Phosphorum\Assets;

use Phalcon\Di;
use Phalcon\Tag;
use Phalcon\Assets\Manager;
use Phalcon\Assets\Collection;
use Phosphorum\Exception\InvalidParameterException;
use Phosphorum\AssetsHash\HashManager\AssetsHashVersion;

/**
 * Phosphorum\Assets\AssetsManagerExtended
 *
 * @package Phosphorum\Assets
 */
class AssetsManagerExtended extends Manager
{
    /**
     * Prints the HTML for JS resources
     *
     * @param string $collectionName the collection name
     * @return string the result of the collection
     **/
    public function cachedOutputJs($collectionName)
    {
        $collection = $this->getCollection($collectionName);
        $name = $this->getFileName($collection);

        if (empty($name)) {
            return $this->outputJs($collectionName);
        }

        $collection->setTargetUri($name);
        return Tag::javascriptInclude($collection->getTargetUri());
    }

    /**
     * Prints the HTML for CSS resources
     *
     * @param string $collectionName the collection name
     * @return string the collection result
     **/
    public function cachedOutputCss($collectionName)
    {
        $collection = $this->getCollection($collectionName);
        $name = $this->getFileName($collection);

        if (empty($name)) {
            return $this->outputCss($collectionName);
        }

        $collection->setTargetUri($name);
        return Tag::stylesheetLink($collection->getTargetUri());
    }

    /**
     * @param string $collectionName
     * @return Collection
     **/
    protected function getCollection($collectionName)
    {
        if ($collectionName === null) {
            throw new InvalidParameterException("Assets collection name mustn't be null");
        }

        return $this->collection($collectionName);
    }

    /**
     * @param Collection $collection
     * @return string
     **/
    protected function getFileName(Collection $collection)
    {
        $hashManager = new AssetsHashVersion($collection);
        $hashManager->setLogger(Di::getDefault()->get('logger'));

        return $hashManager->getHashedFileName();
    }
}
