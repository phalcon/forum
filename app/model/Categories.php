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

use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Resultset\Simple;

/**
 * Categories Model
 *
 * @property Simple posts
 *
 * @method static Categories findFirstById(int $categoryId)
 * @method static Categories[] find($parameters = null)
 * @method Simple getPosts($parameters = null)
 *
 * @package Phosphorum\Model
 */
class Categories extends CacheableModel
{
    public $id;

    public $name;

    public $description;

    public $slug;

    public $number_posts;

    public $no_bounty;

    public $no_digest;

    public function beforeValidation()
    {
        if (!$this->no_bounty) {
            $this->no_bounty = new RawValue('default');
        }

        if (!$this->no_digest) {
            $this->no_digest = new RawValue('default');
        }
    }

    public function getUrl()
    {
        return "category/{$this->id}/{$this->slug}";
    }

    public function initialize()
    {
        $this->hasMany(
            'id',
            'Phosphorum\Model\Posts',
            'categories_id',
            [
                'alias'    => 'posts',
                'reusable' => true
            ]
        );
    }
}
