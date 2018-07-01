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
use Phosphorum\Domain\Entities\PostEntity;
use Phosphorum\Domain\Repositories\PostRepository;
use Phosphorum\Domain\Services\PostService;

/**
 * Phosphorum\Domain\Factories\PostFactory
 *
 * @package Phosphorum\Domain\Factories
 */
class PostFactory implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Creates a PostEntity instance.
     *
     * @return PostEntity
     */
    public function createEntity(): PostEntity
    {
        return $this->getDI()->get(PostEntity::class);
    }

    /**
     * Creates a PostRepository instance.
     *
     * @param  PostEntity $entity
     *
     * @return PostRepository
     */
    public function createRepository(PostEntity $entity): PostRepository
    {
        return $this->getDI()->get(PostRepository::class, [$entity]);
    }

    /**
     * Creates a PostService instance.
     *
     * @return PostService
     */
    public function createService(): PostService
    {
        return $this->getDI()->get(PostService::class, [
            $this->createRepository(
                $this->createEntity()
            ),
            $this->getDI()
        ]);
    }
}
