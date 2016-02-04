<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');
$I->amOnPage('/');
