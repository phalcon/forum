<?php

namespace Helper;

use Codeception\Module;
use Faker\Factory as Faker;
use Phosphorum\Model\Notifications;

/**
 * Notification Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Notification extends Module
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
     * Creates a random notification and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function haveNotification(array $attributes = null)
    {
        $attributes = $attributes ?: [];

        $default = [
            'users_id' => $this->faker->numberBetween(),
            'posts_id' => $this->faker->numberBetween(),
            'posts_replies_id' => $this->faker->numberBetween(),
            'type'  => $this->faker->randomElement(['Y', 'N']),
        ];

        return $this->phalcon->haveRecord(Notifications::class, array_merge($default, $attributes));
    }
}
