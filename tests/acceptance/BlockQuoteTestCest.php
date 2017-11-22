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

class BlockQuoteTestCest
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

    public function shouldAddBlockQuoteBlockToPost(AcceptanceTester $I)
    {
        $I->wantTo("Check blockquote tag in post");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Code < > ' ! <h1>Code content < </h1> `{<h2>Code content2 > ' </h2>}` ";
        $content .= "&nbsp; blockquote text test &nbsp; text after blockquote";

        $postId = $this->post->havePost([
            'title'         => 'Test blockquote in post text',
            'content'       => $content,
            'slug'          => 'test_blockquote',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_blockquote");
        $I->seeInSource('Test blockquote in post text');

        $I->seeInSource("Code &lt; &gt; ' !");
        $I->seeInSource("<h1>Code content &lt; </h1>");
        $I->seeInSource("&lt;h2&gt;Code content2 &gt; ' &lt;/h2&gt;");
        $I->seeInSource("nbsp; blockquote text test &nbsp; text after blockquote");
    }

    public function shouldAddBlockQuoteToReply(AcceptanceTester $I)
    {
        $I->wantTo("Check blockquote tag in reply");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test blockquote in reply text',
            'content'       => 'Test blockquote',
            'slug'          => 'test_reply_blockquote',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_reply_blockquote");
        $I->seeInSource('Test blockquote');

        $replyText = "Code < > ' ! <h1>Code content < </h1> `{<h2>Code content2 > ' </h2>}` ";
        $replyText .= "&nbsp; blockquote text test &nbsp; text after blockquote";

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $user['id'],
            'accepted' => 'N',
            'content'  => $replyText,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_reply_blockquote#{$replyId}");
        $I->seeInSource("Code &lt; &gt; ' !");
        $I->seeInSource("<h1>Code content &lt; </h1>");
        $I->seeInSource("&lt;h2&gt;Code content2 &gt; ' &lt;/h2&gt;");
        $I->seeInSource("nbsp; blockquote text test &nbsp; text after blockquote");
    }
}
