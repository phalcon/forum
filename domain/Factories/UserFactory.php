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

use Phosphorum\Domain\Entities\UserEntity;
use Phosphorum\Domain\Repositories\UserRepository;
use Phosphorum\Domain\Services\UserService;
use Phalcon\Platform\Domain\AbstractFactory;

/**
 * Phosphorum\Domain\Factories\UserFactory
 *
 * @package Phosphorum\Domain\Factories
 */
class UserFactory extends AbstractFactory
{
    /**
     * Creates a UserEntity instance.
     *
     * @return UserEntity
     */
    public function createEntity(): UserEntity
    {
        return $this->getDI()->get(UserEntity::class);
    }

    /**
     * Creates a UserRepository instance.
     *
     * @param  UserEntity $entity
     *
     * @return UserRepository
     */
    public function createRepository(UserEntity $entity): UserRepository
    {
        return $this->getDI()->get(UserRepository::class, [$entity]);
    }

    /**
     * Creates a UserService instance.
     *
     * @return UserService
     */
    public function createService(): UserService
    {
        return $this->getDI()->get(UserService::class, [
            $this->createRepository(
                $this->createEntity()
            )
        ]);
    }
}
