<?php
/**
 * @var \Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('see latest post on front page at top of table');

$userId = $I->amRegularUser();

$catId = $I->haveCategory([
    'name' => 'Database',
    'slug' => 'database',
    'description' => 'Database questions'
]);

$postId = $I->havePost([
    'title' => 'Binding Parameters',
    'content' => 'This may be a little bit of a noob question but here goes.',
    'users_id' => $userId,
    'slug' => 'binding-parameters',
    'categories_id' => $catId
]);

$I->amOnPage('/');
$I->seeInTitle('Discussions - Phalcon Framework');
$I->seeElement('//table/tr[2]/td[2]');
$I->see('Binding Parameters', '//table/tr[2]/td[2]');
$I->click('Binding Parameters');
$I->see('Binding Parameters','h1');
