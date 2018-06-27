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

namespace Phosphorum\Core\Models\Entities;

/**
 * Phosphorum\Core\Models\Entities\RepositoryAwareInterface
 *
 * @package Phosphorum\Core\Entities
 */
interface RepositoryAwareInterface
{
    /**
     * Gets fully qualified repository name.
     *
     * @return string
     */
    public function getRepositoryType(): string;
}
