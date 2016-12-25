<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Model\Services\Service;

use Phalcon\Mvc\Model\Resultset\Simple;
use Phosphorum\Model\Activities as Entity;
use Phosphorum\Model\Users as UsersEntity;
use Phosphorum\Model\Services\AbstractService;

/**
 * Phosphorum\Model\Services\Service\Activities
 *
 * @package Phosphorum\Model\Services\Service
 */
class Activities extends AbstractService
{
    /**
     * Get most active users.
     *
     * Will return Simple result set with fields:
     * - `id`
     * - `login`
     * - `name`
     * - `amount`
     *
     * @param  int $limit
     * @return Simple
     */
    public function getMostActiveUsers($limit = 20)
    {
        /** @var \Phalcon\Mvc\Model\Manager $modelsManager */
        $modelsManager = container('modelsManager');

        return $modelsManager->createBuilder()
            ->from(['a' => Entity::class])
            ->columns(['u.id', 'u.login', 'u.name', 'COUNT(a.users_id) AS amount'])
            ->leftJoin(UsersEntity::class, ' a.users_id = u.id', 'u')
            ->where('u.banned = :banned:', ['banned' => 'N'])
            ->groupBy(['a.users_id'])
            ->orderBy(['a.amount DESC'])
            ->limit($limit)
            ->getQuery()
            ->execute();
    }
}
