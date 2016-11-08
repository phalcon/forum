<?php
/**
 * @issue 85
 * @var   Codeception\Scenario $scenario
 */

$I = new Step\UserSteps($scenario);

$I->wantTo('use underscored character in content and see correct url');

$userId = $I->amRegularUser();
$catId  = $I->haveCategory();
$postId = $I->havePost([
    'title'         => 'Is there a precompiled binary for 64 bit Centos out there',
    'content'       => '[this reddit topic](http://www.reddit.com/r/PHP/comments/2s7bbr/phalconphp_vs_php_disappointing_results/)',
    'users_id'      => $userId,
    'slug'          => 'is-there-a-precompiled-binary-for-64-bit-centos-out-there',
    'categories_id' => $catId
]);


$I->amOnPage('/discussions');
$I->seeInTitle('Discussions - ');
$I->seeLink('Is there a precompiled binary for 64 bit Centos out there', sprintf('/discussion/%s/is-there-a-precompiled-binary-for-64-bit-centos-out-there', $postId));
$I->amOnPage(sprintf('/discussion/%s/is-there-a-precompiled-binary-for-64-bit-centos-out-there', $postId));
$I->seeLink('this reddit topic', 'http://www.reddit.com/r/PHP/comments/2s7bbr/phalconphp%5vs%5php%5disappointing%5results/');
