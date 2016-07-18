<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\UserSteps($scenario);

$I->wantTo('go to the not found page and see flash banner');
$I->amOnPage('/abcdef-jaja');
$I->see('Unfortunately, the page you are requesting can not be found!');
