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
 * Class PostsHistory
 *
 * @property \Phosphorum\Model\Posts post
 *
 * @package Phosphorum\Model
 */
class PostsHistory extends Model
{
    public $id;

    public $posts_id;

    public $users_id;

    public $content;

    public $created_at;

    public function beforeValidationOnCreate()
    {
        $this->created_at = time();
    }

    public function initialize()
    {
        $this->belongsTo('posts_id', Posts::class, 'id', ['alias' => 'post']);
    }

    /**
     * @param Posts $post
     *
     * @return ResultsetInterface|Simple
     */
    public static function findLast(Posts $post)
    {
        return self::find([
            'posts_id = ?0',
            'bind' => [$post->id],
            'order' => 'created_at DESC'
        ]);
    }
}
