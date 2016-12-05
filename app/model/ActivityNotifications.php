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
 * Class ActivityNotifications
 *
 * @property \Phosphorum\Model\Users        user
 * @property \Phosphorum\Model\Posts        post
 * @property \Phosphorum\Model\PostsReplies reply
 *
 * @package Phosphorum\Model
 */
class ActivityNotifications extends Model
{
    public $id;

    public $users_id;

    public $users_origin_id;

    public $type;

    public $posts_id;

    public $posts_replies_id;

    public $created_at;

    public $was_read;

    public $extra;

    public function beforeValidationOnCreate()
    {
        $this->was_read = 'N';
    }

    public function initialize()
    {
        $this->belongsTo(
            'users_id',
            'Phosphorum\Model\Users',
            'id',
            [
                'alias' => 'user'
            ]
        );

        $this->belongsTo(
            'users_origin_id',
            'Phosphorum\Model\Users',
            'id',
            [
                'alias' => 'userOrigin'
            ]
        );

        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            [
                'alias' => 'post'
            ]
        );

        $this->belongsTo(
            'posts_replies_id',
            'Phosphorum\Model\PostsReplies',
            'id',
            [
                'alias' => 'reply'
            ]
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field' => 'created_at'
                ]
            ])
        );
    }

    public function markAsRead()
    {
        if ($this->was_read == 'N') {
            $this->was_read = 'Y';
            $this->save();
        }
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
