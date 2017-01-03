<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class ReplyInDiscussionCest
{
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    /** @var Post */
    protected $post;

    protected function _inject(Category $category, User $user, Post $post)
    {
        $this->user     = $user;
        $this->post     = $post;
        $this->category = $category;
    }

    // tests
    public function replyInADiscussion(FunctionalTester $I)
    {
        $I->wantTo('reply in a discussion');

        $userId = $this->user->amRegularUser();
        $catId  = $this->category->haveCategory();

        $this->post->havePost([
            'title'         => 'Please help with testing',
            'users_id'      => $userId,
            'categories_id' => $catId
        ]);

        $I->amOnPage('/discussions');
        $I->seeLink('Please help with testing');
        $I->click('Please help with testing');
        $I->see('Please help with testing','h1');
        $I->fillField('#content', 'I can do that!');
        $I->click('Add Comment');
        $I->see('I can do that!', '.post-content');
    }
}
