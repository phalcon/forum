<?php

namespace Helper;

use Phalcon\Tag;
use Codeception\Module;
use Faker\Factory as Faker;
use Phosphorum\Model\Categories;

/**
 * Category Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Category extends Module
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \Codeception\Module\Phalcon
     */
    protected $phalcon;

    /**
     * Triggered after module is created and configuration is loaded
     */
    public function _initialize()
    {
        $this->faker = Faker::create();
        $this->phalcon = $this->getModule('Phalcon');
    }

    /**
     * Creates a random category and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveCategory(array $attributes = null)
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

        return $this->phalcon->haveRecord(Categories::class, array_merge($default, $attributes));
    }
}
