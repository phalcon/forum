<?php

use Step\Functional\UserSteps;

class DeletePostsCest
{
    public function deleteADiscussion(FunctionalTester $I, UserSteps $userSteps)
    {
        $userId = $userSteps->amRegularUser();

        $catId = $userSteps->haveCategory(
            [
                'name'        => 'Some Category',
                'slug'        => 'some-category',
                'description' => 'A description of the category'
            ]
        );

        $postId = $userSteps->havePost(
            [
            'title' => 'Is there a way to validate only some fields?',
            'content' => 'as I see, only the form itself can be validated. It validates if all fields passes, right?' .
                        ' Well, this time I have to validate 3 fields - but those fields what passes, should go inside database.' .
                        ' With the original schema, I cant do that',
            'users_id' => $userId,
            'slug' => 'is-there-a-way-to-validate-only-some-fields',
            'categories_id' => $catId
            ]
        );

        $I->amOnPage("/discussion/{$postId}/is-there-a-way-to-validate-only-some-fields");
        $I->seeInTitle('Is there a way to validate only some fields? - Discussion - Phalcon Framework');
        $I->seeElement(['css' => 'a.btn-delete-post']);

        $I->click(['css' => 'a.btn-delete-post']);
        $I->see('Discussion was successfully deleted', '//body/div[1]/div/div/div');
    }
}
