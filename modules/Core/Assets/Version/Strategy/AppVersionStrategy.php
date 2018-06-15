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

namespace Phosphorum\Core\Assets\Version\Strategy;

use Phosphorum\Core\Version;

/**
 * Phosphorum\Core\Assets\Version\Strategy\AppVersionStrategy
 *
 * @package Phosphorum\Core\Assets\Version\Strategy
 */
class AppVersionStrategy extends AbstractStrategy
{
    /**
     * {@inheritdoc}
     *
     * @param  string $fileName
     *
     * @return string
     */
    protected function getHash(string $fileName): string
    {
        return str_replace('.', '', Version::get());
    }
}
