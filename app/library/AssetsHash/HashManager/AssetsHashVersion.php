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
use Phosphorum\Version;

/**
 * Phosphorum\AssetsHash\HashManager\AssetsHashVersion
 *
 * Class uses version in title of file
 *
 * <code>
 * Application won't check changing time resources and collection that was created
 * $hashTime = new AssetsHashTime($collection);
 * $name = $hashTime->getHashedFileName();
 * echo $name.
 * result - collection.341.js
 *
 * Application won't check changing time resources and collection that was created. Using hash as get param
 * $hashTime = new AssetsHashTime($collection, true);
 * $name = $hashTime->getHashedFileName();
 * echo $name.
 * result - collection.js?341
 *
 * Application will check changing time resources and collection that was created. Set folder where resource files are
 * $hashTime = new AssetsHashTime($collection, false, true);
 * $hashTime->setResourceFolder('../public/js');
 * $name = $hashTime->getHashedFileName();
 * echo $name.
 * result - collection.341.js
 * </code>
 *
 * @package Phosphorum\AssetsHash\HashManager
 */
class AssetsHashVersion extends AssetsHashAware
{
    /**
     * Get hash string from forum version
     *
     * @param string $fileName
     * @return string
     */
    protected function getHash($filename)
    {
        $version = str_replace('.', '', Version::get());

        return $version;
    }
}
