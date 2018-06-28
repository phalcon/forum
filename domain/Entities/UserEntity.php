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
 * Phosphorum\Domain\Entities\UserEntity
 *
 * @property Simple badges
 *
 * @method Simple getBadges($parameters = null)
 * @method static UserEntity findFirstById(int $id)
 * @method static UserEntity findFirstByLogin(string $login)
 * @method static UserEntity findFirstByName(string $name)
 * @method static UserEntity findFirstByEmail(string $email)
 * @method static UserEntity findFirstByAccessToken(string $token)
 * @method static UserEntity[] find($parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class UserEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $login;

    /** @var string */
    protected $email;

    /** @var string */
    protected $gravatarId;

    /** @var string */
    protected $tokenType;

    /** @var string */
    protected $accessToken;

    /** @var string */
    protected $notifications;

    /** @var string */
    protected $digest;

    /** @var string */
    protected $timezone;

    /** @var string */
    protected $moderator;

    /** @var int */
    protected $karma;

    /** @var int */
    protected $votes;

    /** @var int */
    protected $votesPoints;

    /** @var string */
    protected $banned;

    /**
     * @deprecated 4.0.0 No longer used.
     * @var string
     */
    protected $theme;

    /** @var int */
    protected $createdAt;

    /** @var int */
    protected $modifiedAt;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users';
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
        $this->hasMany(
            'id',
            UserBadgesEntity::class,
            'users_id',
            ['alias' => 'badges', 'reusable' => true]
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => ['field' => 'created_at'],
                'beforeUpdate' => ['field' => 'modified_at']
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
            'name' => 'name',
            'login' => 'login',
            'email' => 'email',
            'gravatar_id' => 'gravatarId',
            'token_type' => 'tokenType',
            'access_token' => 'accessToken',
            'created_at' => 'createdAt',
            'modified_at' => 'modifiedAt',
            'notifications' => 'notifications',
            'digest' => 'digest',
            'timezone' => 'timezone',
            'moderator' => 'moderator',
            'karma' => 'karma',
            'votes' => 'votes',
            'votes_points' => 'votesPoints',
            'banned' => 'banned',
            'theme' => 'theme',
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
     * @return UserEntity
     */
    public function setId(int $id): UserEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the value of field 'name'.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Method to set the value of field 'name'.
     *
     * @param  string $name
     *
     * @return UserEntity
     */
    public function setName(string $name): UserEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the value of field 'login'.
     *
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Method to set the value of field 'login'.
     *
     * @param  string $login
     *
     * @return UserEntity
     */
    public function setLogin(string $login): UserEntity
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Returns the value of field 'email'.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Method to set the value of field 'email'.
     *
     * @param  string $email
     *
     * @return UserEntity
     */
    public function setEmail(string $email): UserEntity
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the value of field 'gravatar_id'.
     *
     * @return string
     */
    public function getGravatarId(): string
    {
        return $this->gravatarId;
    }

    /**
     * Method to set the value of field 'gravatar_id'.
     *
     * @param  string $gravatarId
     *
     * @return UserEntity
     */
    public function setGravatarId(string $gravatarId): UserEntity
    {
        $this->gravatarId = $gravatarId;

        return $this;
    }

    /**
     * Returns the value of field 'token_type'.
     *
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Method to set the value of field 'token_type'.
     *
     * @param  string $tokenType
     *
     * @return UserEntity
     */
    public function setTokenType(string $tokenType): UserEntity
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    /**
     * Returns the value of field 'access_token'.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Method to set the value of field 'access_token'.
     *
     * @param  string $accessToken
     *
     * @return UserEntity
     */
    public function setAccessToken(string $accessToken): UserEntity
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Returns the value of field 'notifications'.
     *
     * @return string
     */
    public function getNotifications(): string
    {
        return $this->notifications;
    }

    /**
     * Method to set the value of field 'notifications'.
     *
     * @param  string $notifications
     *
     * @return UserEntity
     */
    public function setNotifications(string $notifications): UserEntity
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * Returns the value of field 'digest'.
     *
     * @return string
     */
    public function getDigest(): string
    {
        return $this->digest;
    }

    /**
     * Method to set the value of field 'digest'.
     *
     * @param  string $digest
     *
     * @return UserEntity
     */
    public function setDigest(string $digest): UserEntity
    {
        $this->digest = $digest;

        return $this;
    }

    /**
     * Returns the value of field 'timezone'.
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Method to set the value of field 'timezone'.
     *
     * @param  string $timezone
     *
     * @return UserEntity
     */
    public function setTimezone(string $timezone): UserEntity
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Returns the value of field 'moderator'.
     *
     * @return string
     */
    public function getModerator(): string
    {
        return $this->moderator;
    }

    /**
     * Method to set the value of field 'moderator'.
     *
     * @param  string $moderator
     *
     * @return UserEntity
     */
    public function setModerator(string $moderator): UserEntity
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * Returns the value of field 'karma'.
     *
     * @return int
     */
    public function getKarma(): int
    {
        return $this->karma;
    }

    /**
     * Method to set the value of field 'karma'.
     *
     * @param  int $karma
     *
     * @return UserEntity
     */
    public function setKarma(int $karma): UserEntity
    {
        $this->karma = $karma;

        return $this;
    }

    /**
     * Returns the value of field 'votes'.
     *
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

    /**
     * Method to set the value of field 'votes'.
     *
     * @param  int $votes
     *
     * @return UserEntity
     */
    public function setVotes(int $votes): UserEntity
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Returns the value of field 'votes_points'.
     *
     * @return int
     */
    public function getVotesPoints(): int
    {
        return $this->votesPoints;
    }

    /**
     * Method to set the value of field 'votes_points'.
     *
     * @param  int $votesPoints
     *
     * @return UserEntity
     */
    public function setVotesPoints(int $votesPoints): UserEntity
    {
        $this->votesPoints = $votesPoints;

        return $this;
    }

    /**
     * Returns the value of field 'banned'.
     *
     * @return string
     */
    public function getBanned(): string
    {
        return $this->banned;
    }

    /**
     * Method to set the value of field 'banned'.
     *
     * @param  string $banned
     *
     * @return UserEntity
     */
    public function setBanned(string $banned): UserEntity
    {
        $this->banned = $banned;

        return $this;
    }

    /**
     * Returns the value of field 'theme'.
     *
     * @deprecated 4.0.0 No longer used.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Method to set the value of field 'theme'.
     *
     * @deprecated 4.0.0 No longer used.
     *
     * @param  string $theme
     *
     * @return UserEntity
     */
    public function setTheme(string $theme): UserEntity
    {
        $this->theme = $theme;

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
     * @return UserEntity
     */
    public function setCreatedAt(int $createdAt): UserEntity
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
     * @return UserEntity
     */
    public function setModifiedAt(int $modifiedAt): UserEntity
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
