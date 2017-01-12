<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class PostHistoryCest
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

    public function isNotEditedPost(AcceptanceTester $I)
    {
        $I->wantTo('check history for post which is not edited');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Some title',
            'content'       => 'Some content.',
            'users_id'      => $user['id'],
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$postId}/some-title");
        $I->dontSee('edited');
    }

    public function isEditedPost(AcceptanceTester $I)
    {
        $I->wantTo('check history for post which is edited');

        $userId = $this->user->amRegularUser();
        $catId  = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Some title',
            'content'       => 'Some content.',
            'users_id'      => $userId,
            'categories_id' => $catId,
        ]);

        $I->havePostHistory([
            'posts_id' => $postId,
            'users_id' => $userId,
            'content'  => 'Some content II.',
        ]);

        $I->amOnPage("/discussion/{$postId}/some-title");
        $I->see('edited');
    }
}
