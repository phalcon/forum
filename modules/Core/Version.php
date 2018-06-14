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

namespace Phosphorum\Core;

use Phalcon\Version as PhVersion;

/**
 * Phosphorum\Core\Version
 *
 * @package Phosphorum\Core
 */
class Version extends PhVersion
{
    /**
     * {@inheritdoc}
     *
     * @return array
     * @codingStandardsIgnoreStart
     */
    protected static function _getVersion()
    {
        // @codingStandardsIgnoreEnd
        return [4, 0, 0, 0, 0];
    }
}
