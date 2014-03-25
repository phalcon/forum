<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Class Users
 *
 * @method static Users findFirstById
 * @method static Users findFirstByLogin
 * @method static Users findFirstByName
 * @method static Users findFirstByEmail
 * @method static Users findFirstByAccessToken
 * @method static Users[] find($parameters=null)
 *
 * @package Phosphorum\Models
 */
class Users extends Model
{

    public $id;

    public $name;

    public $login;

    public $email;

    public $token_type;

    public $access_token;

    public $gravatar_id;

    public $created_at;

    public $modified_at;

    public $notifications;

    public $timezone;

    public $moderator;

    public $karma;

    public $votes;

    public $votes_points;

    public function initialize()
    {
        $this->addBehavior(
            new Timestampable(array(
                'beforeCreate' => array(
                    'field' => 'created_at'
                ),
                'beforeUpdate' => array(
                    'field' => 'modified_at'
                )
            ))
        );
    }

    /**
     * @param $karma
     */
    public function increaseKarma($karma)
    {
        $this->karma += $karma;
        $this->votes_points += $karma;
    }

    /**
     * @param $karma
     */
    public function decreaseKarma($karma)
    {
        $this->karma -= $karma;
        $this->votes_points -= $karma;
    }

    public function beforeCreate()
    {
        $this->notifications = 'P';
        $this->moderator     = 'N';
        $this->karma += 45;
        $this->votes_points += 45;
        $this->votes    = 0;
        $this->timezone = 'Europe/London';
    }

    public function afterValidation()
    {
        if ($this->votes_points >= 50) {
            $this->votes++;
            $this->votes_points = 0;
        }
    }

    public function afterCreate()
    {
        if ($this->id > 0) {
            $activity           = new Activities();
            $activity->users_id = $this->id;
            $activity->type     = 'U';
            $activity->save();
        }
    }

    /**
     * @return string
     */
    public function getHumanKarma()
    {
        if ($this->karma >= 1000) {
            return sprintf("%.1f", $this->karma / 1000) . 'k';
        } else {
            return $this->karma;
        }
    }
}
