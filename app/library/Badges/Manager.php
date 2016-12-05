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

namespace Phosphorum\Badges;

use Phosphorum\Model\Users;

/**
 * Phosphorum\Badges\Manager
 */
class Manager
{
    /**
     * Returns instances of all available badges
     *
     * @return BadgeInterface[]
     */
    public function getBadges()
    {
        $badges = [];
        $directory = new \RecursiveDirectoryIterator(__DIR__ . '/Badge');
        foreach ($directory as $item) {
            if (!$item->isDir()) {
                $path = $item->getPathname();
                $baseClassName = str_replace('.php', '', basename($path));

                $className = 'Phosphorum\Badges\Badge\\' . $baseClassName;
                $badge = new $className();

                if ($badge instanceof BadgeInterface) {
                    $badges[] = new $className();
                }
            }
        }

        return $badges;
    }

    /**
     * Process users badges
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
     * @param BadgeInterface[] $badges
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
