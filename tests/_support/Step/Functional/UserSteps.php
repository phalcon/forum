<?php

namespace Step\Functional;

use Codeception\Scenario;
use Faker\Factory as Faker;
use Phalcon\Tag;

class UserSteps extends \FunctionalTester
{
    protected $faker;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->faker = Faker::create();
    }

    /**
     * Creates a random user and return it id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amRegularUser(array $attributes = [])
    {
        $I       = $this;
        $name    = $this->faker->name;
        $login   = $this->faker->userName;
        $default = [
            'name'     => $name,
            'login'    => $login,
            'email'    => $this->faker->email,
            'timezone' => $this->faker->timezone,
        ];


        $attributes = array_merge($default, $attributes);

        $id = $I->haveRecord('Phosphorum\Models\Users', $attributes);
        $I->haveInSession('identity', $id);
        $I->haveInSession('identity-name', $attributes['name']);

        return $id;
    }

    /**
     * Creates an admin user and return it id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amAdmin(array $attributes = [])
    {
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
    public function haveCategory(array $attributes = [])
    {
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

        return $I->haveRecord(
            'Phosphorum\Models\Categories',
            array_merge($default, $attributes)
        );
    }

    /**
     * Creates a random post and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function havePost(array $attributes = [])
    {
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

        return $I->haveRecord(
            'Phosphorum\Models\Posts',
            array_merge($default, $attributes)
        );
    }

    /**
     * Creates a random post reply and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveReply(array $attributes = [])
    {
        $I       = $this;
        $default = [
            'posts_id' => $this->faker->numberBetween(),
            'users_id' => $this->faker->numberBetween(),
            'content'  => $this->faker->paragraph(),
        ];

        return $I->haveRecord(
            'Phosphorum\Models\PostsReplies',
            array_merge($default, $attributes)
        );
    }
}
