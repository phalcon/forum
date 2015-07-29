<?php
/**
 * @var \Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('use the mention name as a link');

$userId = $I->amRegularUser();

$catId = $I->haveCategory([
    'name' => 'Routing',
    'slug' => 'routing',
    'description' => 'Routing questions'
]);

$postId = $I->havePost([
    'title' => 'Router Phalcon',
    'content' => 'I have a question I could not find anywhere, and I ask @123456789, @12er45t and @iregular help. Let me test: @%, @&abcd and xxx@xxx',
    'users_id' => $userId,
    'slug' => 'router-phalcon',
    'categories_id' => $catId
]);

$I->amOnPage('/discussions');
$I->click('Router Phalcon');
$I->see('I have a question I could not find anywhere, and I ask @123456789, @12er45t and @iregular help.', '.post-content');
$I->seeLink('@123456789', '/user/0/123456789');
$I->seeLink('@12er45t', '/user/0/12er45t');
$I->seeLink('@iregular', '/user/0/iregular');
$I->dontSeeLink('@%', '/user/0/%');
$I->dontSeeLink('@&abcd', '/user/0/&abcd');
$I->dontSeeLink('xxx@xxx', '/user/0/xxxxxx');
$I->dontSeeLink('@xxx', '/user/0/xxx');
