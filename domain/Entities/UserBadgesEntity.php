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

/**
 * Phosphorum\Domain\Entities\UserBadgesEntity
 *
 * @package Phosphorum\Domain\Entities
 */
class UserBadgesEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $badge;

    /** @var string */
    protected $type;

    /** @var int */
    protected $code1;

    /** @var int */
    protected $code2;

    /** @var int */
    protected $createdAt;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users_badges';
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
        $this->addBehavior(
            new Timestampable([
                'beforeValidationOnCreate' => ['field' => 'created_at']
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
            'badge' => 'badge',
            'type' => 'type',
            'code1' => 'code1',
            'code2' => 'code2',
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
     * @return UserBadgesEntity
     */
    public function setId(int $id): UserBadgesEntity
    {
        $this->id = $id;

        return $this;
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
     * @param  int $userId
     *
     * @return UserBadgesEntity
     */
    public function setUserId(int $userId): UserBadgesEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the value of field 'badge'.
     *
     * @return string
     */
    public function getBadge(): string
    {
        return $this->badge;
    }

    /**
     * Method to set the value of field 'badge'.
     *
     * @param  string $badge
     *
     * @return UserBadgesEntity
     */
    public function setBadge(string $badge): UserBadgesEntity
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Returns the value of field 'type'.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Method to set the value of field 'type'.
     *
     * @param  string $type
     *
     * @return UserBadgesEntity
     */
    public function setType(string $type): UserBadgesEntity
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the value of field 'code1'.
     *
     * @return int
     */
    public function getCode1(): int
    {
        return $this->code1;
    }

    /**
     * Method to set the value of field 'code1'.
     *
     * @param  int $code1
     *
     * @return UserBadgesEntity
     */
    public function setCode1(int $code1): UserBadgesEntity
    {
        $this->code1 = $code1;

        return $this;
    }

    /**
     * Returns the value of field 'code2'.
     *
     * @return int
     */
    public function getCode2(): int
    {
        return $this->code2;
    }

    /**
     * Method to set the value of field 'code2'.
     *
     * @param  int $code2
     *
     * @return UserBadgesEntity
     */
    public function setCode2(int $code2): UserBadgesEntity
    {
        $this->code2 = $code2;

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
     * @return UserBadgesEntity
     */
    public function setCreatedAt(int $createdAt): UserBadgesEntity
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
