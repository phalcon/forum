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
use Phalcon\Mvc\Model\Row;

/**
 * Phosphorum\Domain\Entities\PostRepliesEntity
 *
 * @property PostEntity post
 * @property PostRepliesEntity postReplyTo
 * @property UserEntity user
 *
 * @method static PostRepliesEntity|bool findFirstById(int $id)
 * @method static PostRepliesEntity|Row|bool findFirst(mixed $parameters = null)
 * @method static Simple find(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class PostRepliesEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $postId;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $inReplyToId;

    /** @var string */
    protected $content;

    /** @var int */
    protected $votesUp;

    /** @var int */
    protected $votesDown;

    /** @var string */
    protected $accepted;

    /** @var int */
    protected $createdAt;

    /** @var int */
    protected $modifiedAt;

    /** @var int */
    protected $editedAt;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'posts_replies';
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
            'postId',
            PostEntity::class,
            'id',
            ['alias' => 'post', 'reusable' => true]
        );

        $this->belongsTo(
            'inReplyToId',
            PostRepliesEntity::class,
            'id',
            ['alias' => 'postReplyTo', 'reusable' => true]
        );

        $this->belongsTo(
            'userId',
            UserEntity::class,
            'id',
            ['alias'=> 'user', 'reusable' => true]
        );

        $this->keepSnapshots(true);

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => ['field' => 'createdAt'],
                'beforeUpdate' => ['field' => 'modifiedAt']
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
            'posts_id' => 'postId',
            'users_id' => 'userId',
            'in_reply_to_id' => 'inReplyToId',
            'content' => 'content',
            'votes_up' => 'votesUp',
            'votes_down' => 'votesDown',
            'accepted' => 'accepted',
            'created_at' => 'createdAt',
            'modified_at' => 'modifiedAt',
            'edited_at' => 'editedAt',
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
     * @return PostRepliesEntity
     */
    public function setId(int $id): PostRepliesEntity
    {
        $this->id = $id;

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
     * @return PostRepliesEntity
     */
    public function setPostId(int $postId): PostRepliesEntity
    {
        $this->postId = $postId;

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
     * @return PostRepliesEntity
     */
    public function setUserId(int $userId): PostRepliesEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the value of field 'in_reply_to_id'.
     *
     * @return int
     */
    public function getInReplyToId(): int
    {
        return $this->inReplyToId;
    }

    /**
     * Method to set the value of field 'in_reply_to_id'.
     *
     * @param  int $inReplyToId
     *
     * @return PostRepliesEntity
     */
    public function setInReplyToId(int $inReplyToId): PostRepliesEntity
    {
        $this->inReplyToId = $inReplyToId;

        return $this;
    }

    /**
     * Returns the value of field 'content'.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Method to set the value of field 'content'.
     *
     * @param  string $content
     *
     * @return PostRepliesEntity
     */
    public function setContent(string $content): PostRepliesEntity
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the value of field 'votes_up'.
     *
     * @return int
     */
    public function getVotesUp(): int
    {
        return $this->votesUp;
    }

    /**
     * Method to set the value of field 'votes_up'.
     *
     * @param  int $votesUp
     *
     * @return PostRepliesEntity
     */
    public function setVotesUp(int $votesUp): PostRepliesEntity
    {
        $this->votesUp = $votesUp;

        return $this;
    }

    /**
     * Returns the value of field 'votes_down'.
     *
     * @return int
     */
    public function getVotesDown(): int
    {
        return $this->votesDown;
    }

    /**
     * Method to set the value of field 'votes_down'.
     *
     * @param  int $votesDown
     *
     * @return PostRepliesEntity
     */
    public function setVotesDown(int $votesDown): PostRepliesEntity
    {
        $this->votesDown = $votesDown;

        return $this;
    }

    /**
     * Returns the value of field 'accepted'.
     *
     * @return string
     */
    public function getAccepted(): string
    {
        return $this->accepted;
    }

    /**
     * Method to set the value of field 'accepted'.
     *
     * @param  string $accepted
     *
     * @return PostRepliesEntity
     */
    public function setAccepted(string $accepted): PostRepliesEntity
    {
        $this->accepted = $accepted;

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
     * @return PostRepliesEntity
     */
    public function setCreatedAt(int $createdAt): PostRepliesEntity
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns the value of field 'modified_at'.
     *
     * @return int
     */
    public function getModifiedAt(): int
    {
        return $this->modifiedAt;
    }

    /**
     * Method to set the value of field 'modified_at'.
     *
     * @param  int $modifiedAt
     *
     * @return PostRepliesEntity
     */
    public function setModifiedAt(int $modifiedAt): PostRepliesEntity
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Returns the value of field 'edited_at'.
     *
     * @return int
     */
    public function getEditedAt(): int
    {
        return $this->editedAt;
    }

    /**
     * Method to set the value of field 'edited_at'.
     *
     * @param  int $editedAt
     *
     * @return PostRepliesEntity
     */
    public function setEditedAt(int $editedAt): PostRepliesEntity
    {
        $this->editedAt = $editedAt;

        return $this;
    }
}
