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

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\ModelInterface;
use Phosphorum\Core\Models\Entities\RepositoryAwareInterface;
use Phosphorum\Core\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Models\Repositories\RepositoryFactory
 *
 * @package Phosphorum\Core\Repositories
 */
class RepositoryFactory implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Create repository by model.
     *
     * @param string $modelClass
     *
     * @return RepositoryInterface
     */
    public function createByModel(string $modelClass): RepositoryInterface
    {
        /** @var ModelInterface $model */
        $model = $this->container->get($modelClass);

        if ($model instanceof RepositoryAwareInterface) {
            return $this->container->get($model->getRepositoryType(), [$model]);
        }

        return $this->container->get(BaseRepository::class, [$model]);
    }
}
