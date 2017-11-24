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

class InjectionTestCest
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

    public function shouldCatchInjectionInPost(AcceptanceTester $I)
    {
        $I->wantTo("Check all injection in post's text");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Some text <script type=\"text/javascript\">alert(\"test\");</script> Text after ";
        $content .= "`text1 <script type=\"text/javascript\">alert(\"test\");</script> text2` ";
        $content .= "<a href=\"javascript:alert(1)\">xss</a> ";
        $content .= "<img src=\"javascript:alert(1)\" alt=\"xss\" /> ";
        $content .= "[xss](https://www.example.com') ";
        $content .= "![xss'](https://www.example2.com) ";

        $postId = $this->post->havePost([
            'title'         => 'Test injection in post text',
            'content'       => $content,
            'slug'          => 'test_injection_post',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_injection_post");
        $I->seeInSource('Test injection in post text');

        $I->seeInSource(
            "Some text &lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt; Text "
        );
        $I->seeInSource(
            "<code>text1 &lt;script type=\"text/javascript\"&gt;alert(\"test\");&lt;/script&gt; text2</code>"
        );
        $I->seeInSource("&lt;a href=&quot;javascript:alert(1)&quot;&gt;xss&lt;/a&gt;");
        $I->seeInSource("&lt;img src=&quot;javascript:alert(1)&quot; alt=&quot;xss&quot; /&gt;");
        $I->seeInSource("<a href=\"https://www.example.com&#039;\">xss</a>");
        $I->seeInSource("<img src=\"https://www.example2.com\" alt=\"xss&#039;");
    }

    public function shouldCatchInjectionInReply(AcceptanceTester $I)
    {
        $I->wantTo("Check all injection in post's reply");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Post's text";

        $postId = $this->post->havePost([
            'title'         => 'Test injection in post reply',
            'content'       => $content,
            'slug'          => 'test_injection_reply',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_injection_reply");
        $I->seeInSource('Test injection in post reply');



        $replyContent = "Some text <script type=\"text/javascript\">alert(\"test\");</script> Text after ";
        $replyContent .= "`text1 <script type=\"text/javascript\">alert(\"test\");</script> text2` ";
        $replyContent .= "<a href=\"javascript:alert(1)\">xss</a> ";
        $replyContent .= "<img src=\"javascript:alert(1)\" alt=\"xss\" /> ";
        $replyContent .= "[xss](https://www.example.com') ";
        $replyContent .= "![xss'](https://www.example2.com) ";
        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $user['id'],
            'accepted' => 'N',
            'content'  => $replyContent,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_injection_reply#{$replyId}");
        $I->seeInSource(
            "Some text &lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt; Text "
        );
        $I->seeInSource(
            "<code>text1 &lt;script type=\"text/javascript\"&gt;alert(\"test\");&lt;/script&gt; text2</code>"
        );
        $I->seeInSource("&lt;a href=&quot;javascript:alert(1)&quot;&gt;xss&lt;/a&gt;");
        $I->seeInSource("&lt;img src=&quot;javascript:alert(1)&quot; alt=&quot;xss&quot; /&gt;");
        $I->seeInSource("<a href=\"https://www.example.com&#039;\">xss</a>");
        $I->seeInSource("<img src=\"https://www.example2.com\" alt=\"xss&#039;");
    }
}
