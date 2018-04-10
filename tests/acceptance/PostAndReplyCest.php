<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class PostAndReplyCest
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

    public function shouldCreatePostWithCorrectSymbols(AcceptanceTester $I)
    {
        $I->wantTo("Check special symbols in post's text");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test special symbols in post text',
            'content'       => "Code < > ' ! <h1>Code content < </h1> `{<h2>Code content2 > ' </h2>}`",
            'slug'          => 'test_spec_sumbol',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_spec_sumbol");
        $I->seeInSource('Test special symbols in post text');

        $I->seeInSource("Code &lt; &gt; ' !");
        $I->seeInSource("&lt;h1&gt;Code content &lt; &lt;/h1&gt;");
        $I->seeInSource("&lt;h2&gt;Code content2 &gt; ' &lt;/h2&gt;");
    }

    public function shouldCreateReplyWithCorrectSymbols(AcceptanceTester $I)
    {
        $I->wantTo("Check special symbols in post's reply");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test special symbols in post reply',
            'content'       => "Test Reply",
            'slug'          => 'test_spec_reply',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_spec_reply");
        $I->seeInSource('Test special symbols in post reply');

        $replyId = $this->post->havePostReply([
            'posts_id' => $postId,
            'users_id' => $user['id'],
            'accepted' => 'N',
            'content'  => "Code < > ' ! <h1>Code content < </h1> `{<h2>Code content > ' </h2>}`",
        ]);

        $I->amOnPage("/discussion/{$postId}/test_spec_reply#{$replyId}");
        $I->seeInSource("Code &lt; &gt; ' !");
        $I->seeInSource("&lt;h1&gt;Code content &lt; &lt;/h1&gt;");
        $I->seeInSource("&lt;h2&gt;Code content &gt; ' &lt;/h2&gt;");
    }
}
