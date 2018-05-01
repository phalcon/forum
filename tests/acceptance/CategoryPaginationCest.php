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

class CategoryPaginationCest
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

    public function shouldCategoryPaginationWorks(AcceptanceTester $I)
    {
        $I->wantTo("Check pagination when category was selected");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = [];
        for ($i = 1; $i <= 41; $i++) {
            $postId[] = $this->post->havePost([
                'title'         => 'Test blockquote in post text',
                'content'       => 'Text post ' . $i,
                'slug'          => 'cat_pagination_' . $i,
                'users_id'      => $user['id'],
                'categories_id' => $catId,
            ]);
        }

        $I->amOnPage("/category/{$catId}/bradtke-ltd");
        $I->click('Test blockquote in post text');
        $I->seeInSource("Text post");

        $I->amOnPage("/category/{$catId}/bradtke-ltd");
        $I->click(['link' => 2]);
        $I->click('Test blockquote in post text');
        $I->seeInSource("Text post");

    }
}
