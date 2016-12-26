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

use Phosphorum\Model\Users as UsersEntity;
use Phosphorum\Model\Posts as PostsEntity;
use Phosphorum\Model\Notifications as Entity;
use Phosphorum\Model\Services\AbstractService;
use Phosphorum\Model\Services\Exception\EntityException;

/**
 * Phosphorum\Model\Services\Service\Notifications
 *
 * @package Phosphorum\Model\Services\Service
 */
class Notifications extends AbstractService
{
    /**
     * Checks if the notification is ready to be sent.
     *
     * @param  Entity $notification
     * @return bool
     */
    public function isReadyToBeSent(Entity $notification)
    {
        return $notification->sent == Entity::STATUS_NOT_SENT;
    }

    /**
     * Checks if the notification has required integrity.
     *
     * @param  Entity $notification
     * @return bool
     */
    public function hasRequiredIntegrity(Entity $notification)
    {
        $post = $notification->post;
        $user = $notification->user;

        return $post instanceof PostsEntity && $user instanceof UsersEntity;
    }

    /**
     * Mark notification as invalid.
     *
     * @param  Entity $notification
     * @throws EntityException
     */
    public function markAsInvalid(Entity $notification)
    {
        $notification->sent = Entity::STATUS_INVALID;

        if (!$notification->save()) {
            throw new EntityException($notification, Entity::class . ' could not be saved.');
        }
    }

    /**
     * Mark notification as skipped.
     *
     * @param  Entity $notification
     * @throws EntityException
     */
    public function markAsSkipped(Entity $notification)
    {
        $notification->sent = Entity::STATUS_SKIPPED;

        if (!$notification->save()) {
            throw new EntityException($notification, Entity::class . ' could not be saved.');
        }
    }
}
