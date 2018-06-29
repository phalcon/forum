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

namespace Phosphorum\Domain\Entities;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\Row;

/**
 * Phosphorum\Domain\Entities\PostTrackingEntity
 *
 * @method static Simple find(mixed $parameters = null)
 * @method static PostTrackingEntity|Row|bool findFirst(mixed $parameters = null)
 * @method static PostTrackingEntity|bool findFirstByUserId(int $id)
 *
 * @package Phosphorum\Domain\Entities
 */
class PostTrackingEntity extends Model
{
    /** @var int */
    protected $userId;

    /** @var int */
    protected $postId;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'topic_tracking';
    }

    /**
     * Keys are the real names in the table and
     * the values their names in the application.
     *
     * @return array
     */
    public function columnMap(): array
    {
        return [
            'user_id' => 'userId',
            'topic_id' => 'postId',
        ];
    }

    /**
     * Returns the value of field 'user_id'.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Method to set the value of field 'user_id'.
     *
     * @param int $userId
     *
     * @return PostTrackingEntity
     */
    public function setUserId(int $userId): PostTrackingEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the value of field 'topic_id'.
     *
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Method to set the value of field 'topic_id'.
     *
     * @param int $postId
     *
     * @return PostTrackingEntity
     */
    public function setPostId(int $postId): PostTrackingEntity
    {
        $this->postId = $postId;

        return $this;
    }
}
