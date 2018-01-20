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

namespace Phosphorum\AssetsHash\HashManager;

use Phosphorum\AssetsHash\AssetsHashAware;

/**
 * Phosphorum\AssetsHash\HashManager\AssetsHashTime
 *
 * Class uses last change time in title of file
 *
 * <code>
 * // Application won't check changing time resources and collection that was created.
 * $hashTime = new AssetsHashTime($collection);
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.1513950863.js
 * echo $name;
 *
 * // Application won't check changing time resources and collection that was created.
 * // Using hash as get param
 * $hashTime = new AssetsHashTime($collection, true);
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.js?1513950863
 * echo $name;
 *
 * // Application will check changing time resources and collection that was created.
 * // Set folder where resource files are
 * $hashTime = new AssetsHashTime($collection, false, true);
 * $hashTime->setResourceFolder('../public/js');
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.1513950863.js
 * echo $name;
 * </code>
 *
 * @package Phosphorum\AssetsHash\HashManager
 */
class AssetsHashTime extends AssetsHashAware
{
    /**
     * Get hash string from time
     *
     * @param string $filename
     * @return string
     */
    protected function getHash($filename)
    {
        return filemtime($filename);
    }
}
