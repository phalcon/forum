<?php

use Helper\Post;
use Helper\User;
use Helper\Category;

class DeletePostsCest
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

    public function deleteADiscussion(FunctionalTester $I)
    {
        $userId = $this->user->amRegularUser();
        $catId  = $this->category->haveCategory();

        $postId = $this->post->havePost(
            [
                'title'         => 'Is there a way to validate only some fields?',
                'users_id'      => $userId,
                'categories_id' => $catId,
            ]
        );

        $I->amOnPage("/discussion/{$postId}/abc");
        $I->seeInTitle('Is there a way to validate only some fields? - Discussion');
        $I->seeElement(['css' => 'a.btn-delete-post']);

        $I->click(['css' => 'a.btn-delete-post']);
        $I->see('Discussion was successfully deleted', '//body/div[1]/div/div/div');
    }
}
