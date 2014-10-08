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
use Phosphorum\Models\Categories;

/**
 * Phosphorum\Badges\Manager
 */
class BadgeBase
{

    protected $noBountyCategories;

    protected $fullNoBountyCategories;

    /**
     * Check whether the user already have this badge
     *
     * @param Users $user
     * @return boolean
     */
    public function has(Users $user)
    {
        return UsersBadges::count(array(
            'users_id = ?0 AND badge = ?1',
            'bind' => array($user->id, $this->getName())
        )) > 0;
    }

    /**
     * Returns the name of the badge
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the description of the badge
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add the badge to ther user
     *
     * @param Users $user
     * @param array $extra
     */
    public function add(Users $user, $extra = null)
    {
        $userBadge = new UsersBadges();
        $userBadge->users_id = $user->id;
        $userBadge->badge = $this->getName();
        var_dump($userBadge->save());
    }

    /**
     * Returns those categories that don't have bounties
     *
     * @return array
     */
    public function getNoBountyCategories()
    {
        if (!$this->noBountyCategories) {
            $categories = array();
            foreach (Categories::find('no_bounty = "Y"') as $category) {
                $categories[] = $category->id;
            }
            $this->noBountyCategories = $categories;
        }
        return $this->noBountyCategories;
    }

    /**
     * Returns those categories that don't have bounties
     *
     * @return array
     */
    public function getFullNoBountyCategories()
    {
        if (!$this->fullNoBountyCategories) {
            $this->fullNoBountyCategories = Categories::find('no_bounty = "Y"')->toArray();
        }
        return $this->fullNoBountyCategories;
    }
}
