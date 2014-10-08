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

namespace Phosphorum\Badges;

use Phosphorum\Models\Users;
use Phosphorum\Models\UsersBadges;

/**
 * Phosphorum\Badges\Manager
 */
class Manager
{
    /**
     * Returns instances of all available badges
     *
     * @return array
     */
    public function getBadges()
    {
        $badges = array();
        $directory = new \RecursiveDirectoryIterator(__DIR__ . '/Badge');
        foreach ($directory as $item) {
            if (!$item->isDir()) {

                $path = $item->getPathname();
                $baseClassName = str_replace('.php', '', basename($path));
                $className = 'Phosphorum\Badges\Badge\\' . $baseClassName;

                $badges[] = new $className();
            }
        }

        return $badges;
    }

    /**
     *
     */
    public function process()
    {
        $badges = $this->getBadges();
        foreach (Users::find() as $user) {
            $this->processUserBadges($user, $badges);
        }
    }

    /**
     * @param Users $user
     * @param array $badges
     */
    public function processUserBadges(Users $user, $badges)
    {
        foreach ($badges as $badge) {
            if (!$badge->has($user)) {
                $extra = $badge->canHave($user);
                if ($extra) {
                    $badge->add($user, $extra);
                }
            }
        }
    }
}
