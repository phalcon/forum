<?php

class ShadowLoginCest
{
    public function login(FunctionalTester $I)
    {
        $I->wantTo('perform shadow login as first user');

        $I->haveInSession('identity', 1);
        $I->haveInSession('identity-name', 'Phalcon');
        $I->amOnPage('/');
        $I->seeInTitle('Discussions - ');
        $I->see('Start a Discussion');
    }
}
