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

namespace Phosphorum\Core\Assets\Version;

use Phalcon\Assets\Collection;

/**
 * Phosphorum\Core\Assets\Version\StrategyInterface
 *
 * @package Phosphorum\Core\Assets\Version
 */
interface StrategyInterface
{
    /**
     * Sets assets collection.
     *
     * @param Collection $collection
     *
     * @return void
     */
    public function setCollection(Collection $collection): void;

    /**
     * Set if the versioning strategy should modify the file name.
     *
     * @param bool $modify
     */
    public function modifyFilename(bool $modify): void;

    /**
     * Is the versioning strategy should modify the file name?
     */
    public function shouldModifyFilename(): bool;

    /**
     * Set if the versioning strategy shoul check modification time in each request.
     *
     * @param bool $check
     */
    public function checkModificationTimeAlways(bool $check): void;

    /**
     * Resolve final file name.
     *
     * @return string|null
     */
    public function resolve();
}
