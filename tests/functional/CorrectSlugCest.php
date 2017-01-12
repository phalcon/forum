<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class CorrectSlugCest
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

    // test
    public function createSlug(FunctionalTester $I)
    {
        $I->wantTo('use illegal characters in title and get correct slug');

        $user   = $this->user->haveUser();
        $catId  = $this->category->haveCategory();
        $postId = $this->post->havePost([
            'title'         => 'model->save() return TRUE when no matching database column',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
            'slug'          => false // do not generate slug manually
        ]);

        $I->amOnPage('/');
        $I->seeInTitle('Discussions - ');
        $I->seeLink('model->save() return TRUE when no matching database column');
        $I->click('model->save() return TRUE when no matching database column');
        $I->seeInCurrentUrl(sprintf('/discussion/%s/modelsave-return-true-when-no-matching-database-column', $postId));
    }
}
