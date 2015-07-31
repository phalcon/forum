<?php
/**
 * @var \Codeception\Scenario $scenario
 */

$I = new Step\Functional\UserSteps($scenario);

$I->wantTo('use illegal characters in title and get correct slug');

$userId = $I->amRegularUser();

$catId = $I->haveCategory([
    'name' => 'ORM',
    'slug' => 'orm',
    'description' => 'ORM related posts'
]);

$postId = $I->havePost([
    'title' => 'model->save() return TRUE when no matching database column',
    'content' => 'some content',
    'users_id' => 1,
    'slug' => 'model-save-return-true-when-no-matching-database-column',
    'categories_id' => $catId
]);

$I->amOnPage('/discussions');
$I->seeInTitle('Discussions - Phalcon Framework');
$I->seeLink('model->save() return TRUE when no matching database column');
$I->click('model->save() return TRUE when no matching database column');
$I->seeInCurrentUrl(sprintf('/discussion/%s/model-save-return-true-when-no-matching-database-column', $postId));



