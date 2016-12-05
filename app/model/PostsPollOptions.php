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
 * Post's Poll Options Model
 *
 * @property Posts post
 *
 * @method static PostsPollOptions findFirstById(int $id)
 * @method Posts getPost($parameters = null)
 *
 * @package Phosphorum\Model
 */
class PostsPollOptions extends Model
{
    public $id;
    public $posts_id;
    public $title;

    public function initialize()
    {
        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            [
                'alias' => 'post',
                'reusable' => true
            ]
        );
    }

    public function afterSave()
    {
        $this->clearCache();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    public function clearCache()
    {
        if ($this->id) {
            $viewCache = $this->getDI()->getShared('viewCache');
            $viewCache->delete('poll-options-' . $this->posts_id);
            $viewCache->delete('poll-options-started-' . $this->posts_id);
        }
    }
}
