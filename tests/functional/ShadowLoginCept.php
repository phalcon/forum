<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new FunctionalTester($scenario);

$I->wantTo('perform shadow login as first user');

$I->haveInSession('identity', 1);
$I->haveInSession('identity-name', 'Phalcon');
$I->amOnPage('/');
$I->seeInTitle('Discussions - Phalcon Framework');
$I->see('Start a Discussion');
