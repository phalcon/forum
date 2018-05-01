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
 * Phosphorum\AssetsHash\HashManager\AssetsHashMd
 *
 * Class uses MD5 hash in title of file
 *
 * <code>
 * // Application won't check changing time resources and collection that was created.
 * $hashTime = new AssetsHashTime($collection);
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.d5a7a190e15c3419db0321c19bc238b4.js
 * echo $name;
 *
 * // Application won't check changing time resources and collection that was created.
 * // Using hash as get param
 * $hashTime = new AssetsHashTime($collection, true);
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.js?d5a7a190e15c3419db0321c19bc238b4
 * echo $name;
 *
 * // Application will check changing time resources and collection that was created.
 * // Set folder where resource files are
 * $hashTime = new AssetsHashTime($collection, false, true);
 * $hashTime->setResourceFolder('../public/js');
 * $name = $hashTime->getHashedFileName();
 *
 * // collection.d5a7a190e15c3419db0321c19bc238b4.js
 * echo $name;
 * </code>
 *
 * @package Phosphorum\AssetsHash\HashManager
 */
class AssetsHashMd extends AssetsHashAware
{
    /**
     * Get hash string from MD5 file hash
     *
     * @param string $filename
     * @return string
     */
    protected function getHash($filename)
    {
        return md5_file($filename);
    }
}
