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

use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model;

/**
 * Post's Poll Votes model
 *
 * @package Phosphorum\Model
 *
 * @method static PostsPollVotes findFirstById(int $id)
 * @method static PostsPollVotes findFirstByUsersId(int $id)
 * @method static PostsPollVotes findFirstByPostsId(int $id)
 * @method static PostsPollVotes findFirstByOptionsId(int $id)
 * @method static Simple findById(int $id)
 * @method static Simple findByUsersId(int $id)
 * @method static Simple findByPostsId(int $id)
 * @method static Simple findByOptionsId(int $id)
 * @method PostsPollOptions getPollOption($parameters = null)
 * @method Posts getPost($parameters = null)
 * @method Users getUser($parameters = null)
 *
 * @property Users user
 * @property Posts post
 * @property PostsPollOptions pollOption
 */
class PostsPollVotes extends Model
{
    public $id;
    public $users_id;
    public $posts_id;
    public $options_id;
    public $created_at;

    public function initialize()
    {
        $this->belongsTo(
            'users_id',
            'Phosphorum\Model\Users',
            'id',
            [
                'alias' => 'user',
                'reusable' => true
            ]
        );

        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            [
                'alias' => 'post',
                'reusable' => true
            ]
        );

        $this->belongsTo(
            'options_id',
            'Phosphorum\Model\PostsPollOptions',
            'id',
            [
                'alias' => 'pollOption',
                'reusable' => true
            ]
        );


        $this->addBehavior(
            new Timestampable([
                'beforeValidationOnCreate' => [
                    'field' => 'created_at'
                ]
            ])
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
            $viewCache->delete("poll-votes-{$this->posts_id}-{$this->users_id}");
        }
    }
}
