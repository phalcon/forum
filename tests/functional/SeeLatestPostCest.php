<?php

use Step\ForumSteps;

class SeeLatestPostCest
{
    public function _before(ForumSteps $I)
    {
        $userId = $I->amRegularUser();
        $catId  = $I->haveCategory();

        $I->havePost([
            'title'         => 'Binding Parameters',
            'content'       => 'This may be a little bit of a noob question but here goes.',
            'users_id'      => $userId,
            'categories_id' => $catId
        ]);
    }

    // tests
    public function browseFrontPage(ForumSteps $I)
    {
        $I->wantTo('see latest post on front page at top of table');

        $I->amOnPage('/');
        $I->seeInTitle('Discussions - ');
        $I->seeElement('.post-positive td');
        $I->see('Binding Parameters', '.post-positive td');
        $I->click('Binding Parameters');
        $I->see('Binding Parameters','h1');
    }
}
