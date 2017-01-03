<?php

use Step\Functional\UserSteps as UserTester;

class ReplyInDiscussionCest
{
    public function _before(UserTester $I)
    {
        $userId = $I->amRegularUser();
        $catId  = $I->haveCategory();

        $I->havePost([
            'title'         => 'Please help with testing',
            'users_id'      => $userId,
            'categories_id' => $catId
        ]);
    }

    // tests
    public function replyInADiscussion(UserTester $I)
    {
        $I->wantTo('reply in a discussion');

        $I->amOnPage('/discussions');
        $I->seeLink('Please help with testing');
        $I->click('Please help with testing');
        $I->see('Please help with testing','h1');
        $I->fillField('#content', 'I can do that!');
        $I->click('Add Comment');
        $I->see('I can do that!', '.post-content');
    }
}
