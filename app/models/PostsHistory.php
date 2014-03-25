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

/**
 * Class PostsHistory
 *
 * @property \Phosphorum\Models\Posts post
 *
 * @package Phosphorum\Models
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
        $this->belongsTo(
            'posts_id',
            'Phosphorum\Models\Posts',
            'id',
            array(
                'alias' => 'post'
            )
        );
    }
}
