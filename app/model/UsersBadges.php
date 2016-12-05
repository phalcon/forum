<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Class UsersBadges
 *
 * @package Phosphorum\Model
 */
class UsersBadges extends Model
{
    public $id;

    public $users_id;

    public $badge;

    public $type;

    public $code1;

    public $code2;

    public $created_at;

    public function initialize()
    {
        $this->addBehavior(new Timestampable(['beforeValidationOnCreate' => ['field' => 'created_at']]));
    }

    public function afterCreate()
    {
        $activity                       = new ActivityNotifications();
        $activity->users_id             = $this->users_id;
        if ($this->type == 'P') {
            $activity->type                 = 'O';
            $activity->posts_id             = $this->code1;
            $activity->posts_replies_id     = 0;
        } else {
            if ($this->type == 'C') {
                $activity->type             = 'V';
                $activity->posts_id         = $this->code2;
                $activity->posts_replies_id = $this->code1;
            } else {
                $activity->type             = 'B';
                $activity->posts_id         = 1;
                $activity->posts_replies_id = 1;
            }
        }
        $activity->extra                = $this->badge;
        $activity->users_origin_id      = $this->users_id;
        $activity->save();
    }
}
