<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class SeeLatestPostCest
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
    public function browseFrontPage(FunctionalTester $I)
    {
        $I->wantTo('see latest post on front page at top of table');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $this->post->havePost([
            'title'         => 'Binding Parameters',
            'content'       => 'This may be a little bit of a noob question but here goes.',
            'users_id'      => $user['id'],
            'categories_id' => $catId
        ]);

        $I->amOnPage('/');
        $I->seeInTitle('Discussions - ');
        $I->seeElement('.post-positive td');
        $I->see('Binding Parameters', '.post-positive td');
        $I->click('Binding Parameters');
        $I->see('Binding Parameters','h1');
    }
}
