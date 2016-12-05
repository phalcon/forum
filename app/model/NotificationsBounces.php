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
 * Class NotificationsBounces
 *
 * @method static NotificationsBounces[] find($parameters=null)
 *
 * @package Phosphorum\Model
 */
class NotificationsBounces extends Model
{

    public $id;

    public $email;

    public $status;

    public $diagnostic;

    public $created_at;

    public $reported;

    const MAX_BOUNCES = 3;

    public function beforeValidationOnCreate()
    {
        $this->reported   = 'N';
        $this->created_at = time();
    }
}
