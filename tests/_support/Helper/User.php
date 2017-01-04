<?php

namespace Helper;

use Codeception\Module;
use Faker\Factory as Faker;
use Phosphorum\Model\Karma;
use Phosphorum\Model\Users;

/**
 * User Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class User extends Module
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
     * Log In as regular user and return its id
     *
     * @param array $attributes Model attributes [Optional]
     * @return int
     */
    public function amRegularUser(array $attributes = null)
    {
        $attributes = $this->haveUser($attributes);

        $this->phalcon->haveInSession('identity', $attributes['id']);
        $this->phalcon->haveInSession('identity-name', $attributes['name']);
        $this->phalcon->haveInSession('identity-karma', $attributes['karma']);

        return $attributes['id'];
    }

    /**
     * Creates a random regular user and return user's attributes
     *
     * @param array $attributes Model attributes [Optional]
     * @return array
     */
    public function haveUser(array $attributes = null)
    {
        $attributes = $attributes ?: [];

        $default = [
            'name'          => $this->faker->userName,
            'login'         => $this->faker->userName,
            'email'         => $this->faker->email,
            'timezone'      => $this->faker->timezone,
            'karma'         => Karma::INITIAL_KARMA + Karma::LOGIN,
            'votes_points'  => Karma::INITIAL_KARMA + Karma::LOGIN,
            'notifications' => Users::NOTIFICATIONS_OFF,
            'digest'        => 'N',
        ];

        $attributes = array_merge($default, $attributes);
        $attributes['id'] = $this->phalcon->haveRecord(Users::class, array_merge($default, $attributes));

        return $attributes;
    }
}
