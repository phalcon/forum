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
*/

namespace Phosphorum\Listener;

use Phalcon\Events\Event;
use Phosphorum\Model\Users;
use Phosphorum\Model\Karma;
use Phosphorum\Model\Activities;
use Phosphorum\Model\UsersSetting;
use Phosphorum\Services\UsersSettingService;

/**
 * Phosphorum\Listener\UsersListener
 *
 * @package Phosphorum\Listener
 */
class UsersListener
{
    public function beforeSave(Event $event, Users $users)
    {
        if (!trim($users->name)) {
            if ($users->login) {
                $users->name = $users->login;
                return;
            }

            $users->name = 'No Name';
        }
    }

    public function beforeCreate(Event $event, Users $users)
    {
        if (!$users->notifications) {
            $users->notifications = Users::NOTIFICATIONS_REP;
        }

        if (!$users->digest) {
            $users->digest = 'Y';
        }

        $users->moderator     = 'Y';
        $users->karma        += Karma::INITIAL_KARMA;
        $users->votes_points += Karma::INITIAL_KARMA;
        $users->votes         = 0;
        $users->timezone      = 'Europe/London';
        $users->theme         = 'D';
        $users->banned        = 'N';
    }

    public function afterValidation(Event $event, Users $users)
    {
        if ($users->votes_points >= 50) {
            $users->votes++;
            $users->votes_points = 0;
        }
    }

    public function afterCreate(Event $event, Users $users)
    {
        if ($users->id <= 0) {
            return;
        }

        $activity           = new Activities();
        $activity->users_id = $users->id;
        $activity->type     = 'U';
        $activity->save();

        (new UsersSetting())->create([
            'userId' => $users->id,
            'jsonData' => (new UsersSettingService)->getDefaultUserExtraData(),
            'createdAt' => time(),
        ]);
    }
}
