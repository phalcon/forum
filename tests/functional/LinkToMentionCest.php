<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class LinkToMentionCest
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
    public function seeLinkToMention(FunctionalTester $I)
    {
        $I->wantTo('use the mention name as a link');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $this->post->havePost([
            'title'         => 'Router Phalcon',
            'content'       => 'I have a question I could not find anywhere, and I ask @123456789, ' .
                               '@12er45t and @iregular help. Let me test: @%, @&abcd and xxx@xxx',
            'users_id'      => $user['id'],
            'categories_id' => $catId
        ]);

        $I->amOnPage('/discussions');
        $I->click('Router Phalcon');
        $I->see('I have a question I could not find anywhere, and I ask @123456789, @12er45t and @iregular help.', '.post-content');
        $I->seeLink('@123456789', '/user/0/123456789');
        $I->seeLink('@12er45t', '/user/0/12er45t');
        $I->seeLink('@iregular', '/user/0/iregular');
        $I->dontSeeLink('@%', '/user/0/%');
        $I->dontSeeLink('@&abcd', '/user/0/&abcd');
        $I->dontSeeLink('xxx@xxx', '/user/0/xxxxxx');
        $I->dontSeeLink('@xxx', '/user/0/xxx');

    }
}
