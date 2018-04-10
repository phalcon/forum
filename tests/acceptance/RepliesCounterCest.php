<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team and contributors               |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

use Helper\Post;
use Helper\User;
use Helper\Category;

class RepliesCounterCest
{
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    /** @var Post */
    protected $post;

    /** @var array */
    protected $data = [];

    protected function _inject(Category $category, User $user, Post $post)
    {
        $this->user     = $user;
        $this->post     = $post;
        $this->category = $category;
    }

    /**
     * Tests replies counter on on `/` page
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>
     * @since  2018-04-10
     */
    public function shouldShowRepliesCounterOnIndexPage(AcceptanceTester $I)
    {
        $I->wantTo("Check amount replies on index page");

        $this->data['post_owner'] = $this->user->haveUser();
        $this->data['user'] = $this->user->haveUser();
        $this->data['catId']  = $this->category->haveCategory([
            'slug' => 'reply_cat'
        ]);

        $this->data['postId'] = $this->post->havePost([
            'title'         => 'Test replies counter',
            'content'       => 'Test replies counter, conten on index page',
            'slug'          => 'test_index_reply',
            'users_id'      => $this->data['post_owner']['id'],
            'categories_id' => $this->data['catId'],
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $this->post->havePostReply([
                'posts_id' => $this->data['postId'],
                'users_id' => $i == 2 ? $this->data['user']['id'] : $this->data['post_owner']['id'],
                'accepted' => 'N',
                'content'  => 'Test replies counter, content reply on index page ' . $i,
            ]);
        }

        $I->amOnPage('/');
        $I->canSee('3', '//span/span');
    }

    /**
     * Tests replies counter on on page with chosen category
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>
     * @since  2018-04-10
     */
    public function shouldShowRepliesCounterOnCategoryPage(AcceptanceTester $I)
    {
        $I->wantTo("Check amount replies on category page");

        $I->amOnPage('/category/'. $this->data['catId'] . '/reply_cat');
        $I->canSee('3', '//span/span');
    }

    /**
     * Tests replies counter on on post's page
     *
     * @test
     * @author Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>
     * @since  2018-04-10
     */
    public function shouldShowRepliesCounterOnPostPage(AcceptanceTester $I)
    {
        $I->wantTo("Check amount replies on post page");

        $I->amOnPage('/discussion/' . $this->data['postId'] . '/test_index_reply');
        $I->canSee('3', '//td/span');
    }
}
