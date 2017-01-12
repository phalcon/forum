<?php

use Codeception\Example;

class ErrorPagesCest
{
    /**
     * @dataprovider pageProvider
     * @param FunctionalTester $I
     * @param Example $example
     */
    public function errorPages(FunctionalTester $I, Example $example)
    {
        $I->wantToTest('error pages and see the relevant status codes and messages');

        $I->amOnPage("/{$example['code']}");
        $I->seeResponseCodeIs($example['code']);
        $I->seeInTitle("{$example['title']} - ");
        $I->canSee($example['message']);
    }

    protected function pageProvider()
    {
        return [
            ['code' => 400, 'title' => 'Bad request', 'message' => 'Something is not quite right.'],
            ['code' => 401, 'title' => 'Authorization required', 'message' => 'To access the requested resource requires authentication.'],
            ['code' => 403, 'title' => 'Access is denied', 'message' => 'Access to this resource is denied by the administrator.'],
            ['code' => 404, 'title' => 'Page not found', 'message' => "Sorry! We can't seem to find the page you're looking for."],
            ['code' => 500, 'title' => 'Something is not quite right', 'message' => 'We’ll be back soon!'],
            ['code' => 503, 'title' => 'Site Maintenance', 'message' => 'Unfortunately an unexpected system error occurred.'],
        ];
    }
}
