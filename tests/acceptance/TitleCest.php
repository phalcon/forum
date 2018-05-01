<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class TitleCest
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

    public function shouldContainWithSpecSymbol(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct title with special symbol");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => "What's f\"oo b<a&r% happened to the forum?",
            'content'       => 'test title with spec symbol',
            'slug'          => 'test_title_spec',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_title_spec");
        $I->seeInSource("What&#039;s f&quot;oo b&lt;a&amp;r% happened to the forum?");
    }

    public function shouldContainXSSProtection(AcceptanceTester $I)
    {
        $I->wantTo("Checking correct title with xss injection");

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => "<script type=\"text/javascript\">alert(\"test\");</script>",
            'content'       => 'test title with xss injection',
            'slug'          => 'test_title_xss',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/test_title_spec");
        $I->seeInSource("&lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt;");
    }
}
