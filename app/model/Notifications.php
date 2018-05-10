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
 * Phosphorum\Model\Notifications
 *
 * @property Users        user
 * @property Posts        post
 * @property PostsReplies reply
 *
 * @package Phosphorum\Model
 */
class Notifications extends Model
{
    const STATUS_NOT_SENT = 'N';
    const STATUS_SENT     = 'Y';
    const STATUS_INVALID  = 'I';
    const STATUS_SKIPPED  = 'S';

    const TYPE_COMMENT = 'C';
    const TYPE_POST    = 'P';

    public $id;

    public $users_id;

    public $type;

    public $posts_id;

    public $posts_replies_id;

    public $created_at;

    public $modified_at;

    public $message_id;

    public $sent;

    public function beforeValidationOnCreate()
    {
        $this->sent = self::STATUS_NOT_SENT;
    }

    public function initialize()
    {
        $this->belongsTo('users_id', Users::class, 'id', ['alias' => 'user']);
        $this->belongsTo('posts_id', Posts::class, 'id', ['alias' => 'post', 'reusable' => true]);
        $this->belongsTo('posts_replies_id', PostsReplies::class, 'id', ['alias' => 'reply']);

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field' => 'created_at'
                ],
                'beforeUpdate' => [
                    'field' => 'modified_at'
                ]
            ])
        );
    }
}
