<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('reply in a discussion');

$userId = $I->amRegularUser();
$catId  = $I->haveCategory();
$postId = $I->havePost([
    'title'         => 'Please help with testing',
    'users_id'      => $userId,
    'categories_id' => $catId
]);

$I->amOnPage("/discussions");
$I->seeLink('Please help with testing');
$I->click('Please help with testing');
$I->see('Please help with testing','h1');
$I->fillField('#content', 'I can do that!');
$I->click('Add Comment');
$I->see('I can do that!', '.post-content');
