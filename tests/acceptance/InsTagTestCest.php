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

class InsTagTestCest
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

    public function shouldAddInsTagToHTMLTreeInPost(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct adding ins tag to HTML tree in post");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $content = "Some text ~~ins text~~ end some text `Some code text ~~ins code text~~ end some code text` ";
        $content .= "~~test tag inside <ins> FooBar </ins> end test~~ ";
        $content .= "test tag outside <ins> FooBar </ins> end test ";
        $content .= "~~test injection <script type=\"text/javascript\">alert(\"test\");</script> end test~~ ";
        $content .= "test2 injection <script type=\"text/javascript\">alert(\"test\");</script> end test2 ";

        $postId = $this->post->havePost([
            'title'         => 'Test ins tag in post text',
            'content'       => $content,
            'slug'          => 'test_ins_tag_post',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_ins_tag_post");
        $I->seeInSource('Test ins tag in post text');
        $I->seeInSource(
            "Some text <ins>ins text</ins> end some text <code>Some code text ~~ins code text~~ end some code text</code>"
        );
        $I->seeInSource("<ins>test tag inside &lt;ins&gt; FooBar &lt;/ins&gt; end test</ins>");
        $I->seeInSource("test tag outside &lt;ins&gt; FooBar &lt;/ins&gt; end test");
        $I->seeInSource(
            "<ins>test injection &lt;script type=\"text/javascript\"&gt;alert(\"test\");&lt;/script&gt; end test</ins>"
        );
        $I->seeInSource(
            "test2 injection &lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt;"
        );
    }

    public function shouldAddInsTagToHTMLTreeInReply(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct adding ins tag to HTML tree in reply");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test ins tag in reply',
            'content'       => 'Test Reply',
            'slug'          => 'test_ins_tag_reply',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_ins_tag_reply");
        $I->seeInSource('Test ins tag in reply');

        $content = "Some text ~~ins text~~ end some text `Some code text ~~ins code text~~ end code text` ";
        $content .= "~~test tag inside <ins> FooBar </ins> end test~~ ";
        $content .= "test tag outside <ins> FooBar </ins> end test ";
        $content .= "~~test injection <script type=\"text/javascript\">alert(\"test\");</script> end test~~ ";
        $content .= "test2 injection <script type=\"text/javascript\">alert(\"test\");</script> end test2 ";

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $user['id'],
            'accepted' => 'N',
            'content'  => $content,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_ins_tag_reply#{$replyId}");
        $I->seeInSource(
            "Some text <ins>ins text</ins> end some text <code>Some code text ~~ins code text~~ end code text</code>"
        );
        $I->seeInSource("<ins>test tag inside &lt;ins&gt; FooBar &lt;/ins&gt; end test</ins>");
        $I->seeInSource("test tag outside &lt;ins&gt; FooBar &lt;/ins&gt; end test");
        $I->seeInSource(
            "<ins>test injection &lt;script type=\"text/javascript\"&gt;alert(\"test\");&lt;/script&gt; end test</ins>"
        );
        $I->seeInSource(
            "test2 injection &lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt; end"
        );
    }
}
