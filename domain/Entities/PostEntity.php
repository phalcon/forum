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
 * Phosphorum\Domain\Entities\PostEntity
 *
 * @property UserEntity user
 * @property CategoryEntity category
 * @property Simple replies
 * @property Simple pollOptions
 *
 * @method UserEntity|Row|bool getUser(mixed $parameters = null)
 * @method static int countByUserId(int $userId)
 * @method static PostEntity|bool findFirstById(int $id)
 * @method static PostEntity|bool findFirstByCategoryId(int $id)
 * @method static Simple findByCategoryId(int $id)
 * @method static PostEntity|Row|bool findFirst(mixed $parameters = null)
 * @method static Simple find(mixed $parameters = null)
 * @method Simple getReplies(mixed $parameters = null)
 * @method Simple getPollOptions(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class PostEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $categoryId;

    /** @var string */
    protected $title;

    /** @var string */
    protected $slug;

    /** @var string */
    protected $content;

    /** @var int */
    protected $numberViews;

    /** @var int */
    protected $numberReplies;

    /** @var int */
    protected $votesUp;

    /** @var int */
    protected $votesDown;

    /** @var string */
    protected $sticked;

    /** @var string */
    protected $status;

    /** @var string */
    protected $locked;

    /** @var int */
    protected $deleted;

    /** @var string */
    protected $acceptedAnswer;

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
        return 'posts';
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
            'categoryId',
            CategoryEntity::class,
            'id',
            [
                'alias' => 'category',
                'reusable' => true,
                'foreignKey' => [
                    'message' => 'The category is not valid'
                ]
            ]
        );

        $this->hasMany(
            'id',
            PollOptionsEntity::class,
            'postId',
            ['alias' => 'pollOptions']
        );

        $this->hasMany(
            'id',
            PostRepliesEntity::class,
            'postId',
            ['alias' => 'replies']
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field' => ['createdAt', 'modifiedAt'],
                ]
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
            'categories_id' => 'categoryId',
            'title' => 'title',
            'slug' => 'slug',
            'content' => 'content',
            'number_views' => 'numberViews',
            'number_replies' => 'numberReplies',
            'votes_up' => 'votesUp',
            'votes_down' => 'votesDown',
            'sticked' => 'sticked',
            'status' => 'status',
            'locked' => 'locked',
            'deleted' => 'deleted',
            'accepted_answer' => 'acceptedAnswer',
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
     * @return PostEntity
     */
    public function setId(int $id): PostEntity
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
     * @return PostEntity
     */
    public function setUserId(int $userId): PostEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the value of field 'categories_id'.
     *
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * Method to set the value of field 'categories_id'.
     *
     * @param  int $categoryId
     *
     * @return PostEntity
     */
    public function setCategoryId(int $categoryId): PostEntity
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Returns the value of field 'title'.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Method to set the value of field 'title'.
     *
     * @param  string $title
     *
     * @return PostEntity
     */
    public function setTitle(string $title): PostEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the value of field 'slug'.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Method to set the value of field 'slug'.
     *
     * @param  string $slug
     *
     * @return PostEntity
     */
    public function setSlug(string $slug): PostEntity
    {
        $this->slug = $slug;

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
     * @return PostEntity
     */
    public function setContent(string $content): PostEntity
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the value of field 'number_views'.
     *
     * @return int
     */
    public function getNumberViews(): int
    {
        return $this->numberViews;
    }

    /**
     * Method to set the value of field 'number_views'.
     *
     * @param  int $numberViews
     *
     * @return PostEntity
     */
    public function setNumberViews(int $numberViews): PostEntity
    {
        $this->numberViews = $numberViews;

        return $this;
    }

    /**
     * Returns the value of field 'number_replies'.
     *
     * @return int
     */
    public function getNumberReplies(): int
    {
        return $this->numberReplies;
    }

    /**
     * Method to set the value of field 'number_replies'.
     *
     * @param  int $numberReplies
     *
     * @return PostEntity
     */
    public function setNumberReplies(int $numberReplies): PostEntity
    {
        $this->numberReplies = $numberReplies;

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
     * @return PostEntity
     */
    public function setVotesUp(int $votesUp): PostEntity
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
     * @return PostEntity
     */
    public function setVotesDown(int $votesDown): PostEntity
    {
        $this->votesDown = $votesDown;

        return $this;
    }

    /**
     * Returns the value of field 'sticked'.
     *
     * @return string
     */
    public function getSticked(): string
    {
        return $this->sticked;
    }

    /**
     * Method to set the value of field 'sticked'.
     *
     * @param  string $sticked
     *
     * @return PostEntity
     */
    public function setSticked(string $sticked): PostEntity
    {
        $this->sticked = $sticked;

        return $this;
    }

    /**
     * Returns the value of field 'status'.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Method to set the value of field 'status'.
     *
     * @param  string $status
     *
     * @return PostEntity
     */
    public function setStatus(string $status): PostEntity
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the value of field 'locked'.
     *
     * @return string
     */
    public function getLocked(): string
    {
        return $this->locked;
    }

    /**
     * Method to set the value of field 'locked'.
     *
     * @param  string $locked
     *
     * @return PostEntity
     */
    public function setLocked(string $locked): PostEntity
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Returns the value of field 'deleted'.
     *
     * @return int
     */
    public function getDeleted(): int
    {
        return $this->deleted;
    }

    /**
     * Method to set the value of field 'deleted'.
     *
     * @param  int $deleted
     *
     * @return PostEntity
     */
    public function setDeleted(int $deleted): PostEntity
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Returns the value of field 'accepted_answer'.
     *
     * @return string
     */
    public function getAcceptedAnswer(): string
    {
        return $this->acceptedAnswer;
    }

    /**
     * Method to set the value of field 'accepted_answer'.
     *
     * @param  string $acceptedAnswer
     *
     * @return PostEntity
     */
    public function setAcceptedAnswer(string $acceptedAnswer): PostEntity
    {
        $this->acceptedAnswer = $acceptedAnswer;

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
     * @return PostEntity
     */
    public function setCreatedAt(int $createdAt): PostEntity
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
     * @return PostEntity
     */
    public function setModifiedAt(int $modifiedAt): PostEntity
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
     * @return PostEntity
     */
    public function setEditedAt(int $editedAt): PostEntity
    {
        $this->editedAt = $editedAt;

        return $this;
    }
}
