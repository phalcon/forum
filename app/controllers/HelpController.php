<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Controllers;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;
use Phosphorum\Models\Notifications;
use Phosphorum\Models\ActivityNotifications;
use Phosphorum\Models\IrcLog;
use Phosphorum\Badges\Manager as BadgeManager;

/**
 * Class HelpController
 *
 * @package Phosphorum\Controllers
 */
class HelpController extends Controller
{

    public function initialize()
    {
        $this->tag->setTitle('Help');
        $this->view->setTemplateBefore(array('discussions'));
    }

    public function indexAction()
    {

    }

    public function karmaAction()
    {

    }

    public function markdownAction()
    {

    }

    public function votingAction()
    {

    }

    public function moderatorsAction()
    {

    }

    public function aboutAction()
    {

    }

    public function createAction()
    {

    }

    public function badgesAction()
    {
        $manager = new BadgeManager;
        $this->view->badges = $manager->getBadges();
    }

    public function statsAction()
    {
        $this->view->threads         = Posts::count();
        $this->view->replies         = Posts::sum(array('column' => 'number_replies'));
        $this->view->votes           = Posts::sum(array('column' => 'votes_up + votes_down'));
        $this->view->users           = Users::count();
        $this->view->karma           = Users::sum(array('column' => 'karma'));
        $this->view->notifications   = Notifications::count();
        $this->view->unotifications  = ActivityNotifications::count();
        $this->view->views           = Posts::sum(array('column' => 'number_views'));
        $this->view->irc             = IrcLog::count();
    }
}
