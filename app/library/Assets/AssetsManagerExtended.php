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

namespace Phosphorum\Assets;

use Phalcon\Assets\Manager;
use Phalcon\Tag;
use Phosphorum\AssetsHash\HashManager\AssetsHashVersion as AssetsHash;
use Phalcon\Di;

/**
 * Add Assets caching
 */
class AssetsManagerExtended extends Manager
{
    /**
     * Prints the HTML for JS resources
     *
     * @param string|null $collectionName the collection name
     * @return string the result of the collection
     **/
    public function cachedOutputJs($collectionName = null)
    {
        $logger = Di::getDefault()->get('logger');
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $collection = $this->collection($collectionName);
        $hashManager = new AssetsHash($collection);
        $hashManager->setLogger(Di::getDefault()->get('logger'));
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $name = $hashManager->getHashedFileName();
        if (empty($name)) {
            $logger->debug(__FILE__ . ' - ' . (string)__LINE__.$name);
            return $this->outputJs($collectionName);
        }
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $collection->setTargetUri($name);
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        return Tag::javascriptInclude($collection->getTargetUri());
    }

    /**
     * Prints the HTML for CSS resources
     *
     * @param string|null $collectionName the collection name
     * @return string the collection result
     **/
    public function cachedOutputCss($collectionName = null)
    {
        $logger = Di::getDefault()->get('logger');
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $collection = $this->collection($collectionName);
        $hashManager = new AssetsHash($collection);
        $hashManager->setLogger(Di::getDefault()->get('logger'));
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $name = $hashManager->getHashedFileName();
        if (empty($name)) {
            return $this->outputCss($collectionName);
        }
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        $collection->setTargetUri($name);
        $logger->debug(__FILE__ . ' - ' . (string)__LINE__);
        return Tag::stylesheetLink($collection->getTargetUri());
    }
}
