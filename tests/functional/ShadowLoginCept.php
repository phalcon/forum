<?php
$I = new TestGuy($scenario);
$I->wantTo('perform shadow login as first user');
$I->haveInSession('identity', 1);
$I->haveInSession('identity-name', 'Phalcon');
$I->amOnPage('/');
$I->see('Logout');
