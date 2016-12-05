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
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Class PostsRepliesHistory
 *
 * @property \Phosphorum\Model\PostsReplies postReply
 *
 * @method static ResultsetInterface|PostsRepliesHistory[] find($parameters = null)
 *
 * @package Phosphorum\Model
 */
class PostsRepliesHistory extends Model
{
    public $id;

    public $posts_replies_id;

    public $users_id;

    public $content;

    public $created_at;

    public function beforeValidationOnCreate()
    {
        $this->created_at = time();
    }

    public function initialize()
    {
        $this->belongsTo('posts_replies_id', PostsReplies::class, 'id', ['alias' => 'postReply']);
    }

    /**
     * @param PostsReplies $reply
     *
     * @return ResultsetInterface|Simple
     */
    public static function findLast(PostsReplies $reply)
    {
        return self::find([
            'posts_replies_id = ?0',
            'bind' => [$reply->id],
            'order' => 'created_at DESC'
        ]);
    }
}
