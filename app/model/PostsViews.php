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
 * Class PostsViews
 *
 * @property \Phosphorum\Model\Posts post
 *
 * @package Phosphorum\Model
 */
class PostsViews extends Model
{

    public $id;

    public $posts_id;

    public $ipaddress;

    public function initialize()
    {
        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            [
                'alias' => 'post'
            ]
        );
    }

    public function clearCache()
    {
        if ($this->id) {
            $viewCache = $this->getDI()->getShared('viewCache');
            $viewCache->delete('post-' . $this->posts_id);
        }
    }
}
