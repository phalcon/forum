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

namespace Phosphorum\Badges\Badge;

use Phosphorum\Models\Users;
use Phosphorum\Models\UsersBadges;
use Phosphorum\Models\PostsVotes;
use Phosphorum\Models\PostsRepliesVotes;
use Phosphorum\Badges\BadgeBase;

/**
 * Phosphorum\Badges\Badge\Supporter
 *
 * First positive vote to another user
 */
class Supporter extends BadgeBase
{

    protected $name = 'Supporter';

    protected $description = 'First positive vote to another user';

    /**
     * Check whether the user can have the badge
     *
     * @param Users $user
     * @return boolean
     */
    public function canHave(Users $user)
    {
        $canHave = PostsRepliesVotes::count(array(
            'users_id = ?0 AND vote = 1',
            'bind' => array($user->id)
        )) > 0;

        $canHave = $canHave || PostsVotes::count(array(
            'users_id = ?0 AND vote = 1',
            'bind' => array($user->id)
        )) > 0;

        return $canHave;
    }
}
