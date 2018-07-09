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

use Phalcon\Platform\Domain\AbstractFactory;
use Phosphorum\Domain\Entities\PostTrackingEntity;
use Phosphorum\Domain\Repositories\PostTrackingRepository;
use Phosphorum\Domain\Services\PostTrackingService;

/**
 * Phosphorum\Domain\Factories\PostTrackingFactory
 *
 * @package Phosphorum\Domain\Factories
 */
class PostTrackingFactory extends AbstractFactory
{
    /**
     * Creates a PostTrackingEntity instance.
     *
     * @return PostTrackingEntity
     */
    public function createEntity(): PostTrackingEntity
    {
        return $this->getDI()->get(PostTrackingEntity::class);
    }

    /**
     * Creates a PostTrackingRepository instance.
     *
     * @param  PostTrackingEntity $entity
     *
     * @return PostTrackingRepository
     */
    public function createRepository(PostTrackingEntity $entity): PostTrackingRepository
    {
        return $this->getDI()->get(PostTrackingRepository::class, [$entity]);
    }

    /**
     * Creates a PostTrackingService instance.
     *
     * @return PostTrackingService
     */
    public function createService(): PostTrackingService
    {
        return $this->getDI()->get(PostTrackingService::class, [
            $this->createRepository(
                $this->createEntity()
            )
        ]);
    }
}
