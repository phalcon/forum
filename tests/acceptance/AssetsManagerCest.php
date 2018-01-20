<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class AssetsManagerCest
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

    public function shouldCreateCssJsCollections(AcceptanceTester $I)
    {
        $I->wantTo("Check created js and css collection");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test assets manager',
            'content'       => 'Testing css and js collection',
            'slug'          => 'test_assets',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_assets");
        $I->seeInSource('Testing css and js collections');
        $I->seeInSource('/assets/global');
        $I->dontSeeInSource('/css/bootstrap.min.css');
        $I->seeFileFound('global.js','public/assets');
        $I->seeFileFound('global.css','public/assets');
    }
}
