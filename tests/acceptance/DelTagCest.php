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

class DelTagCest
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

    public function shouldAddDelTagToHTMLTreeInPost(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct adding del tag to HTML tree in post");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Some text ~del text~ end some text `Some code text ~del code text~ end some code text` ";
        $content .= "~test tag inside <del> FooBar </del> end test~ ";
        $content .= "test tag outside <del> FooBar </del> end test ";
        $content .= "~test injection <script type=\"text/javascript\">alert(\"test\");</script> end test~ ";
        $content .= "test2 injection <script type=\"text/javascript\">alert(\"test\");</script> end test2 ";

        $postId = $this->post->havePost([
            'title'         => 'Test del tag in post text',
            'content'       => $content,
            'slug'          => 'test_del_tag_post',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_del_tag_post");
        $I->seeInSource('Test del tag in post text');

        $resContent = "Some text <del>del text</del> end some text ";
        $resContent .= "<code>Some code text ~del code text~ end some code text</code>";
        $I->seeInSource($resContent);

        $I->seeInSource("<del>test tag inside &lt;del&gt; FooBar &lt;/del&gt; end test</del>");
        $I->seeInSource("test tag outside &lt;del&gt; FooBar &lt;/del&gt; end test");

        $resContent = "<del>test injection &lt;script type=\"text/javascript\"&gt;";
        $resContent .= "alert(\"test\");&lt;/script&gt; end test</del>";
        $I->seeInSource($resContent);

        $resContent = "test2 injection &lt;script type=&quot;text/javascript&quot;&gt;";
        $resContent .= "alert(&quot;test&quot;);&lt;/script&gt; end test2";
        $I->seeInSource($resContent);
    }

    public function shouldAddDelTagToHTMLTreeInReply(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct adding del tag to HTML tree in reply");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test del tag in reply',
            'content'       => 'Test Reply',
            'slug'          => 'test_del_tag_reply',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_del_tag_reply");
        $I->seeInSource('Test del tag in reply');

        $content = "Some text ~del text~ end some text `Some code text ~del code text~ end code text` ";
        $content .= "~test tag inside <del> FooBar </del> end test~ ";
        $content .= "test tag outside <del> FooBar </del> end test ";
        $content .= "~test injection <script type=\"text/javascript\">alert(\"test\");</script> end test~ ";
        $content .= "test2 injection <script type=\"text/javascript\">alert(\"test\");</script> end test2 ";

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $user['id'],
            'accepted' => 'N',
            'content'  => $content,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_ins_tag_reply#{$replyId}");

        $resContent = "Some text <del>del text</del> end some text ";
        $resContent .= "<code>Some code text ~del code text~ end code text</code>";
        $I->seeInSource($resContent);

        $I->seeInSource("<del>test tag inside &lt;del&gt; FooBar &lt;/del&gt; end test</del>");
        $I->seeInSource("test tag outside &lt;del&gt; FooBar &lt;/del&gt; end test");

        $resContent = "<del>test injection &lt;script type=\"text/javascript\"&gt;";
        $resContent .= "alert(\"test\");&lt;/script&gt; end test</del>";
        $I->seeInSource($resContent);

        $resContent = "test2 injection &lt;script type=&quot;text/javascript&quot;&gt;";
        $resContent .= "alert(&quot;test&quot;);&lt;/script&gt; end test2";
        $I->seeInSource($resContent);
    }
}
