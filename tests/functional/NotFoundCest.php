<?php

class NotFoundCest
{
    public function showBanner(FunctionalTester $I)
    {
        $I->wantTo('go to the not found page and see flash banner');

        $I->amOnPage('/abcdef-jaja');
        $I->seeResponseCodeIs(404);
        $I->see("Sorry! We can't seem to find the page you're looking for.");
    }
}
