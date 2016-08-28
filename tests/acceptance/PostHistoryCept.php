<?php
/**
 * @var Codeception\Scenario $scenario
 */

use Step\UserSteps;
use Phosphorum\Models\Posts;

class PostHistoryCept
{
    protected $postIds = [];

    public function isNotEditedPost(AcceptanceTester $I, UserSteps $userSteps)
    {
        $I->wantTo('check history for post which is not edited');

        $userId = $userSteps->amRegularUser();
        $catId  = $userSteps->haveCategory();

        $post = new Posts([
            'title'         => 'Some title',
            'content'       => 'Some content.',
            'users_id'      => $userId,
            'categories_id' => $catId,
        ]);

        $I->amOnPage("/discussion/{$post->id}/some-title");
        $I->dontSee('edited');
    }

    public function isEditedPost(AcceptanceTester $I, UserSteps $userSteps)
    {
        $I->wantTo('check history for post which is edited');

        $userId = $userSteps->amRegularUser();
        $catId  = $userSteps->haveCategory();

        $postId = $I->havePost([
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
