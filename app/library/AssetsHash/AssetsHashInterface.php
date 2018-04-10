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

/**
 * Phosphorum\AssetsHash\AssetsHashInterface
 *
 * Interface for hash in title of assets collection
 *
 * @package Phosphorum\AssetsHash
 */
interface AssetsHashInterface
{
    /**
     * Set assets collection
     *
     * @param Collection $collection
     */
    public function setCollection(Collection $collection);

    /**
     * Get File name with hash
     *
     * @return string
     */
    public function getHashedFileName();
}
