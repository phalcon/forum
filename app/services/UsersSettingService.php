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

namespace Phosphorum\Services;

use Phalcon\Di;
use Phosphorum\Model\UsersSetting;

/**
 * Class UsersSettingService
 * Phosphorum\Services\UsersSettingService
 *
 * @package Phosphorum\Services
 */
class UsersSettingService
{
    public function getDataOrDefault(int $userId)
    {
        $useData = UsersSetting::findFirstByUserId($userId);

        if ($useData) {
            return json_decode($useData->jsonData, true);
        }

        return json_decode($this->getDefaultUserExtraData(), true);
    }

    public function getDefaultUserExtraData(): string
    {
        return Di::getDefault()->get('filesystem')->read('config/users_data.json');
    }
}
