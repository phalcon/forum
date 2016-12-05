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
use Phosphorum\Model\PostsVotes;
use Phosphorum\Model\PostsRepliesVotes;
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
        $canHave = PostsRepliesVotes::count([
            'users_id = ?0 AND vote = 1',
            'bind' => [$user->id]
        ]) > 0;

        $canHave = $canHave || PostsVotes::count([
            'users_id = ?0 AND vote = 1',
            'bind' => [$user->id]
        ]) > 0;

        return $canHave;
    }
}
