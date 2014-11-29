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

use Phalcon\Db\RawValue;

/**
 * Class Categories
 *
 * @method static Categories findFirstById
 * @method static Categories[] find($parameters = null)
 *
 * @package Phosphorum\Models
 */
class Categories extends CacheableModel
{

    public $id;

    public $name;

    public $slug;

    public $number_posts;

    public $no_bounty;

    public $no_digest;

    public function beforeValidation()
    {
        if (! $this->no_bounty) {
            $this->no_bounty = new RawValue('default');
        }

        if (! $this->no_digest) {
            $this->no_digest = new RawValue('default');
        }
    }
}
