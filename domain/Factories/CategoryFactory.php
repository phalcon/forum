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

namespace Phosphorum\Domain\Factories;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;
use Phosphorum\Domain\Entities\CategoryEntity;
use Phosphorum\Domain\Repositories\CategoryRepository;
use Phosphorum\Domain\Services\CategoryService;

/**
 * Phosphorum\Domain\Factories\CategoryFactory
 *
 * @package Phosphorum\Domain\Factories
 */
class CategoryFactory implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Creates a PostTrackingEntity instance.
     *
     * @return CategoryEntity
     */
    public function createEntity(): CategoryEntity
    {
        return $this->getDI()->get(CategoryEntity::class);
    }

    /**
     * Creates a PostTrackingRepository instance.
     *
     * @param  CategoryEntity $entity
     *
     * @return CategoryRepository
     */
    public function createRepository(CategoryEntity $entity): CategoryRepository
    {
        return $this->getDI()->get(CategoryRepository::class, [$entity]);
    }

    /**
     * Creates a PostTrackingService instance.
     *
     * @return CategoryService
     */
    public function createService(): CategoryService
    {
        return $this->getDI()->get(CategoryService::class, [
            $this->createRepository(
                $this->createEntity()
            )
        ]);
    }
}
