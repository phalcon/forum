<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\UserSteps($scenario);

$I->wantTo('start a discussion');

$I->amRegularUser();
$I->haveCategory(['name' => 'Testing']);
$I->amOnPage('/');
$I->see('Start a Discussion');
$I->click('Start a Discussion');
$I->see('Start a Discussion', 'h1');
$I->seeRecord('Phosphorum\Models\Categories', ['name' => 'Testing']);
$I->fillField('#title', 'How can I write tests in Codeception');
$I->fillField('#content', 'Is there any manual');
$I->selectOption('#categoryId', 'Testing');
$I->click('Submit Discussion');
$I->see('How can I write tests in Codeception','h1');
$I->seeInCurrentUrl('/how-can-i-write-tests-in-codeception');
