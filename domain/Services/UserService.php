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

namespace Phosphorum\Domain\Services;

use Phalcon\Platform\Domain\AbstractService;
use Phalcon\Platform\Domain\Exceptions\EntityNotFoundException;
use Phosphorum\Domain\Entities\UserEntity;
use Phosphorum\Domain\Repositories\UserRepository;

/**
 * Phosphorum\Domain\Services\UserService
 *
 * @method UserRepository getRepository()
 *
 * @package Phosphorum\Domain\Services
 */
class UserService extends AbstractService
{
    /**
     * PostTrackingService constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Counts how many users in the database.
     *
     * @return int
     */
    public function countAll(): int
    {
        return $this->getRepository()->count();
    }

    /**
     * Gets last registered user.
     *
     * @return UserEntity
     *
     * @throws EntityNotFoundException
     */
    public function getLastRegisteredUser(): UserEntity
    {
        /** @var \Phalcon\Mvc\Model\Resultset\Simple $lastMember */
        $lastMember = $this->getRepository()->find([
            'order' => 'createdAt DESC',
            'limit' => 1
        ]);

        if ($lastMember->valid() == false || $lastMember->count() == 0) {
            throw new EntityNotFoundException(UserEntity::class);
        }

        return $lastMember->getFirst();
    }
}
