<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class CorrectConvertUriCest
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

    public function convertUri(FunctionalTester $I)
    {
        $I->wantTo('use underscored character in content and see correct url');

        $user   = $this->user->haveUser();
        $catId  = $this->category->haveCategory();
        $postId = $this->post->havePost([
            'title'         => 'Is there a precompiled binary for 64 bit Centos out there',
            'content'       => '[this reddit topic](http://www.reddit.com/r/PHP/comments/2s7bbr/phalconphp_vs_php_disappointing_results/)',
            'users_id'      => $user['id'],
            'slug'          => 'is-there-a-precompiled-binary',
            'categories_id' => $catId
        ]);

        $I->amOnPage('/discussions');
        $I->seeInTitle('Discussions - ');
        $I->seeLink('Is there a precompiled binary for 64 bit Centos out there', sprintf('/discussion/%s/is-there-a-precompiled-binary', $postId));
        $I->amOnPage(sprintf('/discussion/%s/is-there-a-precompiled-binary', $postId));
        $I->seeLink('this reddit topic', 'http://www.reddit.com/r/PHP/comments/2s7bbr/phalconphp%5vs%5php%5disappointing%5results/');
    }
}
