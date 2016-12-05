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

namespace Phosphorum\Badges\Badge;

use Phosphorum\Model\Users;
use Phosphorum\Badges\BadgeBase;

/**
 * Phosphorum\Badges\Badge\Veteran
 *
 * More than one year in the forum and more than 1000 of karma
 */
class Veteran extends BadgeBase
{
    protected $name = 'Veteran';

    protected $description = 'More than one year in the forum and more than 1000 of karma';

    /**
     * Check whether the user can have the badge
     *
     * @param Users $user
     * @return boolean
     */
    public function canHave(Users $user)
    {
        $date = new \DateTime();
        $date->modify('-1 year');
        return $user->karma >= 1000 && $user->created_at < $date->getTimestamp();
    }
}
