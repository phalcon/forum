<?php
$I = new TestGuy\UserSteps($scenario);
$I->wantTo('reply in a discussion');
$user_id = $I->amAdmin();
$cat_id = $I->haveCategory(array(
    'name' => 'Testing',
    'slug' => 'test',
    'description' => 'codeception functional test'
));
$post_id = $I->havePost(array(
	'title' => 'Please help with testing', 
	'content' => 'How can I install Codeception', 
	'users_id' => $user_id,
	'slug' => 'please-help-with-testing',
	'categories_id' => $cat_id
));
$I->amOnPage("/discussions");
$I->seeLink('Please help with testing');
$I->click('Please help with testing');
$I->see('Please help with testing','h1');
$I->fillField('#content', 'I can do that!');
$I->click('Add Comment');
$I->see('I can do that!', '.post-content');
