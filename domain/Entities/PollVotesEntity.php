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
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Resultset\Simple;

/**
 * Phosphorum\Domain\Entities\PollVotesEntity
 *
 * @property UserEntity user
 * @property PostEntity post
 * @property PollOptionsEntity pollOption
 *
 * @method static PollVotesEntity|bool findFirstById(int $id)
 * @method static PollVotesEntity|bool findFirstByUsersId(int $id)
 * @method static PollVotesEntity|bool findFirstByPostsId(int $id)
 * @method static PollVotesEntity|bool findFirstByOptionsId(int $id)
 * @method static Simple findById(int $id)
 * @method static Simple findByUsersId(int $id)
 * @method static Simple findByPostsId(int $id)
 * @method static Simple findByOptionsId(int $id)
 * @method PollOptionsEntity getPollOption(mixed $parameters = null)
 * @method PostEntity getPost(mixed $parameters = null)
 * @method UserEntity getUser(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class PollVotesEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $pollOptionId;

    /** @var int */
    protected $postId;

    /** @var int */
    protected $createdAt;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'posts_poll_votes';
    }

    /**
     * Aids in setting up the model with a custom behavior and
     * relationships (if any).
     *
     * NOTE: This method is only called once during the request,
     * it’s intended to perform initializations that apply for all
     * instances of the model created within the application.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->belongsTo(
            'userId',
            UserEntity::class,
            'id',
            ['alias' => 'user', 'reusable' => true]
        );

        $this->belongsTo(
            'postId',
            PostEntity::class,
            'id',
            ['alias' => 'post', 'reusable' => true]
        );

        $this->belongsTo(
            'pollOptionId',
            PollOptionsEntity::class,
            'id',
            ['alias' => 'pollOption', 'reusable' => true]
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => ['field' => 'createdAt']
            ])
        );
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
            'id' => 'id',
            'users_id' => 'userId',
            'options_id' => 'pollOptionId',
            'posts_id' => 'postId',
            'created_at' => 'createdAt',
        ];
    }

    /**
     * Returns the value of field 'id'.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Method to set the value of field 'id'.
     *
     * @param  int $id
     *
     * @return PollVotesEntity
     */
    public function setId(int $id): PollVotesEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the value of field 'users_id'.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Method to set the value of field 'users_id'.
     *
     * @param  int $userId
     *
     * @return PollVotesEntity
     */
    public function setUserId(int $userId): PollVotesEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the value of field 'options_id'.
     *
     * @return int
     */
    public function getPollOptionId(): int
    {
        return $this->pollOptionId;
    }

    /**
     * Method to set the value of field 'options_id'.
     *
     * @param  int $pollOptionId
     *
     * @return PollVotesEntity
     */
    public function setPollOptionId(int $pollOptionId): PollVotesEntity
    {
        $this->pollOptionId = $pollOptionId;

        return $this;
    }

    /**
     * Returns the value of field 'posts_id'.
     *
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Method to set the value of field 'posts_id'.
     *
     * @param  int $postId
     *
     * @return PollVotesEntity
     */
    public function setPostId(int $postId): PollVotesEntity
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Returns the value of field 'created_at'.
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Method to set the value of field 'created_at'.
     *
     * @param  int $createdAt
     *
     * @return PollVotesEntity
     */
    public function setCreatedAt(int $createdAt): PollVotesEntity
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
