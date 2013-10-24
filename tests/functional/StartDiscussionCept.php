<?php
$I = new TestGuy\UserSteps($scenario);
$I->wantTo('start a discussion');
$I->amAdmin();
$I->amOnPage('/');
$I->see('Start a Discussion');
$I->click('Start a Discussion');
$I->fillField('#title', 'How can I write tests in Codeception');
$I->click('Submit Discussion');
$I->see('How can I write tests in Codeception');

