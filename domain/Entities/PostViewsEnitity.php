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

/**
 * Phosphorum\Domain\Entities\PostViewsEnitity
 *
 * @property PostEntity $post
 *
 * @method Simple getPost(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class PostViewsEnitity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $postId;

    /** @var string */
    protected $ipAddress;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'posts_views';
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
            ['alias' => 'post']
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
            'ipaddress' => 'ipAddress',
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
     * @return PostViewsEnitity
     */
    public function setId(int $id): PostViewsEnitity
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
     * @return PostViewsEnitity
     */
    public function setPostId(int $postId): PostViewsEnitity
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Returns the value of field 'ipaddress'.
     *
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Method to set the value of field 'ipaddress'.
     *
     * @param  string $ipAddress
     *
     * @return PostViewsEnitity
     */
    public function setIpAddress(string $ipAddress): PostViewsEnitity
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }
}
