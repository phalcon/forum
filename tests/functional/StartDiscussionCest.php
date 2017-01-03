<?php

use Helper\User;
use Helper\Category;
use Phosphorum\Model\Categories;

class StartDiscussionCest
{
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    protected function _inject(Category $category, User $user)
    {
        $this->user     = $user;
        $this->category = $category;
    }

    // tests
    public function startDiscussion(FunctionalTester $I)
    {
        $I->wantTo('start a discussion');

        $this->user->amRegularUser(['karma' => 301]);
        $this->category->haveCategory(['name' => 'Testing']);

        $I->amOnPage('/');
        $I->see('Start a Discussion');
        $I->click('Start a Discussion');
        $I->see('Start a Discussion', 'h1');
        $I->seeRecord(Categories::class, ['name' => 'Testing']);
        $I->fillField('#title', 'How can I write tests in Codeception');
        $I->fillField('#content', 'Is there any manual');
        $I->selectOption('#categoryId', 'Testing');
        $I->click('Submit Discussion');
        $I->see('How can I write tests in Codeception','h1');
        $I->seeInCurrentUrl('/how-can-i-write-tests-in-codeception');
    }
}
