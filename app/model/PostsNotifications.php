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

/**
 * Class PostsNotifications
 *
 * @property \Phosphorum\Model\Posts post
 *
 * @package Phosphorum\Model
 */
class PostsNotifications extends Model
{

    public $id;

    public $posts_id;

    public $users_id;

    public function initialize()
    {
        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            array(
                'alias' => 'post'
            )
        );
    }
}
