<?php

/*
   +------------------------------------------------------------------------+
   | Phosphorum                                                             |
   +------------------------------------------------------------------------+
   | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
   +------------------------------------------------------------------------+
   | This source file is subject to the New BSD License that is bundled     |
   | with this package in the file LICENSE.txt.                             |
   |                                                                        |
   | If you did not receive a copy of the license and are unable to         |
   | obtain it through the world-wide-web, please send an email             |
   | to license@phalconphp.com so we can send you a copy immediately.       |
   +------------------------------------------------------------------------+
   | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
   +------------------------------------------------------------------------+
 */

use Helper\Post;
use Helper\User;
use Helper\Category;

class AssetsManagerTestCest
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
        $I->seeInSource('Testing css and js collection');
        $I->seeInSource('/assets/globalCss');
        $I->seeInSource('/assets/globalJs');
        $I->dontSeeInSource('/css/bootstrap.min.css');
        $I->seeFileFound('globalJs.js','public/assets');
        $I->seeFileFound('globalCss.css','public/assets');
    }
}
