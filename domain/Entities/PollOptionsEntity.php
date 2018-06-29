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
use Phalcon\Mvc\Model\Row;

/**
 * Phosphorum\Domain\Entities\PollOptionsEntity
 *
 * @property PostEntity $post
 *
 * @method static PollOptionsEntity|bool findFirstById(int $id)
 * @method PostEntity|Row|bool getPost(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class PollOptionsEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $postsId;

    /** @var string */
    protected $title;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'posts_poll_options';
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
            'title' => 'title',
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
     * @return PollOptionsEntity
     */
    public function setId(int $id): PollOptionsEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the value of field 'posts_id'.
     *
     * @return int
     */
    public function getPostsId(): int
    {
        return $this->postsId;
    }

    /**
     * Method to set the value of field 'posts_id'.
     *
     * @param  int $postsId
     *
     * @return PollOptionsEntity
     */
    public function setPostsId(int $postsId): PollOptionsEntity
    {
        $this->postsId = $postsId;

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
     * @return PollOptionsEntity
     */
    public function setTitle(string $title): PollOptionsEntity
    {
        $this->title = $title;

        return $this;
    }
}
