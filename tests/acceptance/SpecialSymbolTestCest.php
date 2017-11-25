<?php

/*
   +------------------------------------------------------------------------+
   | Phalcon forum                                                          |
   +------------------------------------------------------------------------+
   | Copyright (c) 2011-2017 Phalcon Team (https://www.phalconphp.com)      |
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

class SpecialSymbolTestCest
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

    public function shouldHaveCodeSpecialSymbolInContent(AcceptanceTester $I)
    {
        $I->wantTo("Check special symbols in post's code tags");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Should have <' `<h1>Code content < </h1> {{ partial('partials/listings') }} `";
        $content .= "This is the content that's in the db right `Code again <' `";

        $postId = $this->post->havePost([
            'title'         => 'Test special symbols in post text',
            'content'       => $content,
            'slug'          => 'test_special_sumbol',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_special_sumbol");
        $I->seeInSource('Test special symbols in post text');

        $I->seeInSource("{ partial('partials/listings') }");
        $I->seeInSource("Should have &lt;'");
        $I->seeInSource("Code again &lt;'");
    }

    public function shouldHaveAllSpecialSymbolsInContent(AcceptanceTester $I)
    {
        $I->wantTo("Check all special symbols in post's code tags");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Should have <' `<h1>Code content < </h1> {{ partial('partials/listings') }}` is the content that's in the db";
        $content .= "<ins>test ins1 tag</ins> right `test code2` <ins>test ins2 tag</ins> text <del>test del tag</del>";

        $postId = $this->post->havePost([
            'title'         => 'Test all special symbols in post text',
            'content'       => $content,
            'slug'          => 'test_all_special_sumbol',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_all_special_sumbol");
        $I->seeInSource('Test all special symbols in post text');

        $I->seeInSource("{ partial('partials/listings') }");
        $I->seeInSource("Should have &lt;'");
        $I->seeInSource("&lt;ins&gt;test ins1 tag&lt;/ins&gt;");
        $I->seeInSource("right <code>test code2</code>");
        $I->seeInSource("&lt;ins&gt;test ins2 tag&lt;/ins&gt;");
        $I->seeInSource("&lt;del&gt;test del tag&lt;/del&gt;");
    }

    public function shouldHaveDelInsSpecialSymbolsInContent(AcceptanceTester $I)
    {
        $I->wantTo("Check del and ins tags in post's content");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Should have <' <ins>test ins1 tag</ins> right ";
        $content .= "<ins>test ins2 tag</ins> text <del>test del tag</del>";

        $postId = $this->post->havePost([
            'title'         => 'Test del and ins tags in post text',
            'content'       => $content,
            'slug'          => 'test_del_ins_tags',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_all_special_sumbol");
        $I->seeInSource('Test del and ins tags in post text');

        $I->seeInSource("Should have &lt;'");
        $I->seeInSource("&lt;ins&gt;test ins1 tag&lt;/ins&gt;");
        $I->seeInSource("&lt;ins&gt;test ins2 tag&lt;/ins&gt;");
        $I->seeInSource("&lt;del&gt;test del tag&lt;/del&gt;");
    }
}
