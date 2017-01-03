<?php

namespace Step;

use Phalcon\Tag;
use Phosphorum\Model\Karma;
use Phosphorum\Model\Users;
use Phosphorum\Model\Posts;
use Phosphorum\Model\Categories;

/**
 * Trait FakerTrait
 *
 * @package Step
 *
 * @property \Faker\Generator $faker
 */
trait FakerTrait
{
    /**
     * Creates a random category and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveCategory($attributes = null)
    {
        $attributes = $attributes ?: [];

        $name    = $this->faker->company;
        $default = [
            'name'         => $name,
            'slug'         => Tag::friendlyTitle($name),
            'description'  => $this->faker->sentence,
            'number_posts' => $this->faker->numberBetween(),
            'no_bounty'    => $this->faker->randomElement(['Y', 'N']),
            'no_digest'    => $this->faker->randomElement(['Y', 'N']),
        ];

        // do not generate slug manually
        if (array_key_exists('slug', $attributes) && $attributes['slug'] === false) {
            unset($attributes['slug'], $default['slug']);
        }

        return $this->haveRecord(Categories::class, array_merge($default, $attributes));
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

        return $this->haveRecord(Posts::class, array_merge($default, $attributes));
    }

    /**
     * Creates a random regular user and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amRegularUser(array $attributes = null)
    {
        $attributes = $attributes ?: [];

        $default = [
            'name'         => $this->faker->userName,
            'login'        => $this->faker->userName,
            'email'        => $this->faker->email,
            'timezone'     => $this->faker->timezone,
            'karma'        => Karma::INITIAL_KARMA + Karma::LOGIN,
            'votes_points' => Karma::INITIAL_KARMA + Karma::LOGIN,
        ];

        $attributes = array_merge($default, $attributes);

        $id = $this->haveRecord(Users::class, $attributes);

        $this->haveInSession('identity', $id);
        $this->haveInSession('identity-name', $attributes['name']);
        $this->haveInSession('identity-karma', $attributes['karma']);

        return $id;
    }
}
