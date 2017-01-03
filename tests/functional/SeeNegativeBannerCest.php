<?php

use Helper\Post;

class SeeNegativeBannerCest
{
    /** @var Post */
    protected $post;

    protected function _inject(Post $post)
    {
        $this->post = $post;
    }

    public function seeNegativeBanner(FunctionalTester $I)
    {
        $I->wantTo('see banner with info about too many negative votes');

        $postId = $this->post->havePost([
            'title'         => 'Negative reputation',
            'content'       => 'This may be a little bit of a noob question but here goes.',
            'users_id'      => 1,
            'categories_id' => 1,
            'votes_up'      => 0,
            'votes_down'    => 5,
        ]);

        $I->amOnPage("/discussion/{$postId}/negative-reputation");

        $I->see('Too many negative votes', '//body/div/div[1]/div[2]/h4');
        $I->see('This post has too many negative votes. The cause of this could be:', '//body/div/div[1]/div[2]/p');
        $I->see('Irrelevant or controversial information', '//body/div/div[1]/div[2]/ul/li[1]');
        $I->see('Confusing question or not a real question', '//body/div/div[1]/div[2]/ul/li[2]');
        $I->see('Aggressive vocabulary, excessive rudeness, etc', '//body/div/div[1]/div[2]/ul/li[3]');
    }
}
