<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Numericality;

/**
 * Class UsersSetting
 * Phosphorum\Model\UsersSetting
 *
 * @property Users user
 * @property $userId
 * @property $jsonData
 * @property $createdAt
 * @property $modifiedAt
 *
 * @method static UsersSetting findFirstByUserId(int $userId)
 *
 * @package Phosphorum\Model
 */
class UsersSetting extends Model
{
    public $id;

    public $userId;

    public $jsonData;

    public $createdAt;

    public $modifiedAt;

    public function initialize()
    {
        $this->belongsTo(
            'user_id',
            Users::class,
            'id',
            [
                'alias'    => 'user',
                'reusable' => true
            ]
        );
    }

    public function columnMap()
    {
        return [
            "id"          => "id",
            "user_id"     => "userId",
            "json_data"   => "jsonData",
            "created_at"  => "createdAt",
            "modified_at" => "modifiedAt",
        ];
    }
}
