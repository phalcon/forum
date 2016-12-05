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
use Phosphorum\Model\UsersBadges;
use Phosphorum\Badges\BadgeBase;

/**
 * Phosphorum\Badges\Badge\GoodReply
 *
 * Awarded one time per every question with more than 5 positive votes
 */
class GoodReply extends BadgeBase
{
    protected $name = 'Good Reply';

    protected $description = 'Awarded one time per every answer with more than 5 positive votes';

    /**
     * Check whether the user already have this badge
     *
     * @param Users $user
     * @return boolean
     */
    public function has(Users $user)
    {
        $has = false;
        $noBountyCategories = $this->getNoBountyCategories();
        $conditions = '(IF(votes_up IS NULL, 0, votes_up) - IF(votes_down IS NULL, 0, votes_down)) >= 5';
        $replies = $user->getReplies([$conditions, 'columns' => 'id', 'order' => 'created_at DESC']);
        foreach ($replies as $reply) {
            $has |= (UsersBadges::count([
                'users_id = ?0 AND badge = ?1 AND type = "C" AND code1 = ?2',
                'bind' => [$user->id, $this->getName(), $reply->id]
            ]) == 0);
        }
        return !$has;
    }

    /**
     * Check whether the user can have the badge
     *
     * @param  Users $user
     * @return boolean
     */
    public function canHave(Users $user)
    {
        $ids = [];
        $noBountyCategories = $this->getNoBountyCategories();
        $conditions = '(IF(votes_up IS NULL, 0, votes_up) - IF(votes_down IS NULL, 0, votes_down)) >= 5';
        $replies = $user->getReplies([$conditions, 'columns' => 'id, posts_id', 'order' => 'created_at DESC']);
        foreach ($replies as $reply) {
            $have = UsersBadges::count([
                'users_id = ?0 AND badge = ?1 AND type = "C" AND code1 = ?2',
                'bind' => [$user->id, $this->getName(), $reply->id]
            ]);
            if (!$have) {
                $ids[] = [$reply->posts_id, $reply->id];
            }
        }
        return $ids;
    }

    /**
     * Add the badge to the user
     *
     * @param Users $user
     * @param array $extra
     * @return $this
     */
    public function add(Users $user, $extra = null)
    {
        $name = $this->getName();
        foreach ($extra as $reply) {
            $userBadge = new UsersBadges();
            $userBadge->users_id = $user->id;
            $userBadge->badge    = $name;
            $userBadge->type     = 'C';
            $userBadge->code1    = $reply[1];
            $userBadge->code2    = $reply[0];

            $userBadge->save();
        }

        return $this;
    }
}
