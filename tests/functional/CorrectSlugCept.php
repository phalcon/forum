<?php
/**
 * @issue 87
 * @var   Codeception\Scenario $scenario
 */

$I = new Step\UserSteps($scenario);

$I->wantTo('use illegal characters in title and get correct slug');

$userId = $I->amRegularUser();
$catId  = $I->haveCategory();
$postId = $I->havePost([
    'title'         => 'model->save() return TRUE when no matching database column',
    'users_id'      => $userId,
    'categories_id' => $catId,
    'slug'          => false // do not generate slug manually
]);

$I->amOnPage('/');
$I->seeInTitle('Discussions - ');
$I->seeLink('model->save() return TRUE when no matching database column');
$I->click('model->save() return TRUE when no matching database column');
$I->seeInCurrentUrl(sprintf('/discussion/%s/modelsave-return-true-when-no-matching-database-column', $postId));
