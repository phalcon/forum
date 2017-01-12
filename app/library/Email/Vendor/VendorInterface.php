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

namespace Phosphorum\Email\Vendor;

/**
 * Phosphorum\Email\Vendor\VendorInterface
 *
 * @package Phosphorum\Email\Vendor
 */
interface VendorInterface
{
    /**
     * Gets vendor id.
     *
     * @return int
     */
    public function getId();

    /**
     * Gets vendor name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets vendor regexp.
     *
     * @return string
     */
    public function getVendorRegexp();

    /**
     * Whether the vendor has non empty regexp.
     *
     * @return bool
     */
    public function hasNonEmptyRegexp();
}
