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
 * Phosphorum\Email\Vendor\AbstractVendor
 *
 * @package Phosphorum\Email\Vendor
 */
abstract class AbstractVendor implements VendorInterface
{
    const ID     = 0;
    const NAME   = 'Unknown';
    const REGEXP = '';

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function getId()
    {
        return static::ID;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getVendorRegexp()
    {
        return static::REGEXP;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasNonEmptyRegexp()
    {
        return !empty(static::REGEXP);
    }
}
