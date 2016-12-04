<?php
/**
 * @var Codeception\Scenario $scenario
 */

$I = new Step\UserSteps($scenario);

$I->wantToTest('error pages and see the relevant status codes and messages');

$errors = [
    400 => [
        'Bad request',
        'Something is not quite right.',
    ],
    401 => [
        'Authorization required',
        'To access the requested resource requires authentication.',
    ],
    403 => [
        'Access is denied',
        'Access to this resource is denied by the administrator.',
    ],
    404 => [
        'Page not found',
        "Sorry! We can't seem to find the page you're looking for.",
    ],
    500 => [
        'Something is not quite right',
        'We’ll be back soon!',
    ],
    503 => [
        'Site Maintenance',
        'Unfortunately an unexpected system error occurred.',
    ],
];


foreach ($errors as $code => $see) {
    $I->amOnPage("/{$code}");
    $I->seeResponseCodeIs($code);
    $I->seeInTitle($see[0] . ' - ');
    $I->canSee($see[1]);
}
