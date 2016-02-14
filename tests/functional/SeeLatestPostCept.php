<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('see latest post on front page at top of table');

$userId = $I->amRegularUser();
$catId  = $I->haveCategory();
$postId = $I->havePost([
    'title'         => 'Binding Parameters',
    'content'       => 'This may be a little bit of a noob question but here goes.',
    'users_id'      => $userId,
    'categories_id' => $catId
]);

$I->amOnPage('/');
$I->seeInTitle('Discussions - Phalcon Framework');
$I->seeElement('.post-positive td');
$I->see('Binding Parameters', '.post-positive td');
$I->click('Binding Parameters');
$I->see('Binding Parameters','h1');
