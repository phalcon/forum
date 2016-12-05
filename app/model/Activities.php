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
 * Class Activities
 *
 * @property Users user
 * @property Posts post
 *
 * @package Phosphorum\Model
 */
class Activities extends Model
{
    public $id;

    public $users_id;

    public $type;

    public $posts_id;

    public $created_at;

    const NEW_POST = 'P';
    const NEW_REPLY = 'C';

    public function initialize()
    {
        $this->belongsTo('users_id', Users::class, 'id', ['alias' => 'user', 'reusable' => true]);

        $this->belongsTo('posts_id', Posts::class, 'id', ['alias' => 'post', 'reusable' => true]);

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field' => 'created_at'
                ]
            ])
        );
    }

    /**
     * @return bool|string
     */
    public function getHumanCreatedAt()
    {
        $diff = time() - $this->created_at;
        if ($diff > (86400 * 30)) {
            return date('M \'y', $this->created_at);
        } else {
            if ($diff > 86400) {
                return ((int)($diff / 86400)) . 'd ago';
            } else {
                if ($diff > 3600) {
                    return ((int)($diff / 3600)) . 'h ago';
                } else {
                    return ((int)($diff / 60)) . 'm ago';
                }
            }
        }
    }
}
