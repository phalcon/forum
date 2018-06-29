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

namespace Phosphorum\Core\Domain;

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

/**
 * Phosphorum\Core\Domain\RepositoryInterface
 *
 * @package Phosphorum\Core\Domain
 */
interface RepositoryInterface
{
    /**
     * Get related enity.
     *
     * @return ModelInterface
     */
    public function getEntity(): ModelInterface;

    /**
     * Find a specific record.
     *
     * @param int|string|array $parameters
     *
     * @return ResultsetInterface
     */
    public function find($parameters = null): ResultsetInterface;
}
