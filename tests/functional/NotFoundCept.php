<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('go to the not found page and see flash banner');
$I->amOnPage('/abcdef-jaja');
$I->see('Page not found: /abcdef-jaja', '//body/div[1]/div/div/div');
