<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon forum                                                          |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2017 Phalcon Team (https://www.phalconphp.com)      |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
  +------------------------------------------------------------------------+
*/

class AuthorizationCest
{
    public function shouldCheckAutorizationOnForumWithGit(AcceptanceTester $I)
    {
        $I->wantTo('check authorization on the forum with github');

        $I->amOnPage('/discussions/hot');
        $I->click('Log In');
        $I->fillField('login', 'test_application@ukr.net');
        $I->fillField('password', 'test123456');
        $I->click('Sign in');

        if (in_array('Reauthorization required', $I->grabMultiple('h2'))) {
            $I->click('#js-oauth-authorize-btn');
        }

        $I->see('Welcome');
        $I->canSeeInCurrentUrl('/discussions');

        $I->amOnPage('/discussions/hot');
        $I->click(".//*[@id='forum-navbar-collapse']/ul/li[8]/a");
        $I->see('Goodbye!');
    }

    public function shouldAuthorizationOnGitAndForum(AcceptanceTester $I)
    {
        $I->wantTo('check authorization on the github after that on the forum');

        $I->amOnUrl('https://github.com');
        $I->amOnPage('/login');
        $I->fillField('login', 'test_application@ukr.net');
        $I->fillField('password', 'test123456');
        $I->click('commit');
        $I->see('Learn Git and GitHub without any code!');

        $I->amOnUrl($_SERVER['APP_URL']);
        $I->amOnPage('/');
        $I->see('Hot');
        $I->amOnPage('/discussions/hot');
        $I->canSeeInCurrentUrl('/discussions/hot');

        $I->click('Log In');
        if (in_array('Reauthorization required', $I->grabMultiple('h2'))) {
            $I->click('#js-oauth-authorize-btn');
        }

        $I->see('Welcome');
        $I->canSeeInCurrentUrl('/discussions');

        $I->amOnPage('/discussions/hot');
        $I->click(".//*[@id='forum-navbar-collapse']/ul/li[8]/a");
        $I->see('Goodbye!');
    }
}
