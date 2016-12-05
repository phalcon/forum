<?php

use Phalcon\Tag;
use Phosphorum\Model\Posts;
use Phosphorum\Model\Users;
use Phosphorum\Model\Categories;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Model\PostsHistory;

/**
 * Helper Trait
 *
 * @property \Faker\Generator $faker
 */
trait HelperTrait
{
    /**
     * Creates a random user and return it id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amRegularUser($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $name    = $this->faker->name;
        $login   = $this->faker->userName;
        $default = [
            'name'     => $name,
            'login'    => $login,
            'email'    => $this->faker->email,
            'timezone' => $this->faker->timezone,
            'karma'    => 400
        ];

        $attributes = array_merge($default, $attributes);

        $id = $I->haveRecord(Users::class, $attributes);
        $I->haveInSession('identity', $id);
        $I->haveInSession('identity-name', $attributes['name']);
        $I->haveInSession('identity-karma', $attributes['karma']);

        return $id;
    }

    /**
     * Creates an admin user and return it id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amAdmin($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $default = [
            'name'     => 'Phalcon',
            'login'    => 'phalcon',
            'email'    => $this->faker->email,
            'timezone' => $this->faker->timezone,
        ];

        $attributes = array_merge($default, $attributes);

        return $I->amRegularUser($attributes);
    }

    /**
     * Creates a random category and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveCategory($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $name    = $this->faker->company;
        $default = [
            'name'         => $name,
            'slug'         => Tag::friendlyTitle($name),
            'description'  => $this->faker->sentence,
            'number_posts' => (int) $this->faker->boolean(),
            'no_bounty'    => $this->faker->boolean() ? 'Y' : 'N',
            'no_digest'    => $this->faker->boolean() ? 'Y' : 'N',
        ];

        // do not generate slug manually
        if (array_key_exists('slug', $attributes) && $attributes['slug'] === false) {
            unset($attributes['slug'], $default['slug']);
        }

        return $I->haveRecord(Categories::class, array_merge($default, $attributes));
    }

    /**
     * Creates a random post and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function havePost($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $title   = $this->faker->title;
        $default = [
            'title'         => $title,
            'slug'          => Tag::friendlyTitle($title),
            'content'       => $this->faker->text(),
            'users_id'      => $this->faker->numberBetween(),
            'categories_id' => $this->faker->numberBetween(),
        ];

        // do not generate slug manually
        if (array_key_exists('slug', $attributes) && $attributes['slug'] === false) {
            unset($attributes['slug'], $default['slug']);
        }

        return $I->haveRecord(Posts::class, array_merge($default, $attributes));
    }

    /**
     * Creates a random post history and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function havePostHistory($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $default = [
            'posts_id' => $this->faker->numberBetween(),
            'users_id' => $this->faker->numberBetween(),
            'content'  => $this->faker->paragraph(),
        ];

        return $I->haveRecord(PostsHistory::class, array_merge($default, $attributes));
    }

    /**
     * Creates a random post reply and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveReply($attributes = null)
    {
        $attributes = $attributes ?: [];

        $I       = $this;
        $default = [
            'posts_id' => $this->faker->numberBetween(),
            'users_id' => $this->faker->numberBetween(),
            'content'  => $this->faker->paragraph(),
        ];

        return $I->haveRecord(PostsReplies::class, array_merge($default, $attributes));
    }
}
