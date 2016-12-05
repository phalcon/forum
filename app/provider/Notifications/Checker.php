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

namespace Phosphorum\Provider\Notifications;

use Phosphorum\Model\ActivityNotifications;

/**
 * Phosphorum\Provider\Notifications\Checker
 *
 * Checks if the user has unread notifications or not
 *
 * @package Phosphorum\Provider\Notifications
 */
class Checker
{
    /**
     * Check whether there are unread notifications or not
     *
     * @return boolean
     */
    public function has()
    {
        $usersId = container('session')->get('identity');
        if (!$usersId) {
            return false;
        }

        $number = ActivityNotifications::count([
            'users_id = ?0 AND was_read = "N"',
            'bind' => [$usersId]
        ]);

        return $number > 0;
    }

    /**
     * Check whether there are unread notifications or not
     *
     * @return integer
     */
    public function getNumber()
    {
        $usersId = container('session')->get('identity');
        if (!$usersId) {
            return 0;
        }

        $number = ActivityNotifications::count([
            'users_id = ?0 AND was_read = "N"',
            'bind' => [$usersId]
        ]);

        return $number;
    }
}
