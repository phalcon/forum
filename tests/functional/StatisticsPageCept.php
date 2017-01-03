<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new FunctionalTester($scenario);

$I->wantTo('get forum statistics');

$I->amOnPage('/help/stats');

$I->see('Statistics', 'h2');
$I->see('The most active users', 'h2');
