<?php

use Step\Functional\UserSteps;

class DeletePostsCest
{
    public function deleteADiscussion(FunctionalTester $I, UserSteps $userSteps)
    {
        $userId = $userSteps->amRegularUser();
        $catId  = $userSteps->haveCategory();

        $postId = $userSteps->havePost(
            [
                'title'         => 'Is there a way to validate only some fields?',
                'users_id'      => $userId,
                'categories_id' => $catId,
            ]
        );

        $I->amOnPage("/discussion/{$postId}/abc");
        $I->seeInTitle('Is there a way to validate only some fields? - Discussion - Phalcon Framework');
        $I->seeElement(['css' => 'a.btn-delete-post']);

        $I->click(['css' => 'a.btn-delete-post']);
        $I->see('Discussion was successfully deleted', '//body/div[1]/div/div/div');
    }
}
