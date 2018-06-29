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
 * Phosphorum\Domain\Entities\CategoryEntity
 *
 * @property Simple $posts
 *
 * @method Simple getPosts(mixed $parameters = null)
 *
 * @package Phosphorum\Domain\Entities
 */
class CategoryEntity extends Model
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $slug;

    /** @var int */
    protected $numberPosts;

    /** @var string */
    protected $noBounty;

    /** @var string */
    protected $noNigest;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'categories';
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
            PostEntity::class,
            'categoryId',
            ['alias' => 'posts', 'reusable' => true]
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
            'slug' => 'slug',
            'number_posts' => 'number_posts',
            'no_bounty' => 'no_bounty',
            'no_digest' => 'no_digest',
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
     * @return CategoryEntity
     */
    public function setId(int $id): CategoryEntity
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
     * @return CategoryEntity
     */
    public function setName(string $name): CategoryEntity
    {
        $this->name = $name;

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
     * @return CategoryEntity
     */
    public function setSlug(string $slug): CategoryEntity
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Returns the value of field 'number_posts'.
     *
     * @return int
     */
    public function getNumberPosts(): int
    {
        return $this->numberPosts;
    }

    /**
     * Method to set the value of field 'number_posts'.
     *
     * @param  int $numberPosts
     *
     * @return CategoryEntity
     */
    public function setNumberPosts(int $numberPosts): CategoryEntity
    {
        $this->numberPosts = $numberPosts;

        return $this;
    }

    /**
     * Returns the value of field 'no_bounty'.
     *
     * @return string
     */
    public function getNoBounty(): string
    {
        return $this->noBounty;
    }

    /**
     * Method to set the value of field 'no_bounty'.
     *
     * @param  string $noBounty
     *
     * @return CategoryEntity
     */
    public function setNoBounty(string $noBounty): CategoryEntity
    {
        $this->noBounty = $noBounty;

        return $this;
    }

    /**
     * Returns the value of field 'no_digest'.
     *
     * @return string
     */
    public function getNoNigest(): string
    {
        return $this->noNigest;
    }

    /**
     * Method to set the value of field 'no_digest'.
     *
     * @param  string $noNigest
     *
     * @return CategoryEntity
     */
    public function setNoNigest(string $noNigest): CategoryEntity
    {
        $this->noNigest = $noNigest;

        return $this;
    }
}
