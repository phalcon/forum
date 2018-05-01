<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class PostLinkCest
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

    public function shouldFollowTheLink(AcceptanceTester $I)
    {
        $I->wantTo('Follow the correct link');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test link',
            'content'       => 'Content begin. http://imgs.xkcd.com/comics/exploits_of_a_mom.png Content end',
            'slug'          => 'test_correct_link',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_correct_link");
        $I->seeInSource('Test link');
        $I->seeInSource("href=\"http://imgs.xkcd.com/comics/exploits_of_a_mom.png\"");
    }

    public function shouldFollowTheLinkSecondOption(AcceptanceTester $I)
    {
        $I->wantTo('Follow the correct link');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test link second',
            'content'       => 'Test content. [test-content](https://imgs.xkcd.com/comics/exploits_of_a_mom.png)',
            'slug'          => 'test_correct_link_second',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_correct_link_second");
        $I->seeInSource('Test link second');
        $I->seeInSource("href=\"https://imgs.xkcd.com/comics/exploits_of_a_mom.png\"");

    }
}
