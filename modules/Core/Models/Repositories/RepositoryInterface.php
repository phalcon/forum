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

namespace Phosphorum\Core\Models\Repositories;

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

/**
 * Phosphorum\Core\Models\Repositories\RepositoryInterface
 *
 * @package Phosphorum\Core\Repositories
 */
interface RepositoryInterface
{
    /**
     * Get related model.
     *
     * @return ModelInterface
     */
    public function getModel(): ModelInterface;

    /**
     * Find a specific record.
     *
     * @param int|string|array $parameters
     *
     * @return null|ResultsetInterface
     */
    public function find($parameters): ?ResultsetInterface;
}
