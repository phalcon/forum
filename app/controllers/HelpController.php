<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
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
class HelpController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore(['discussions']);
    }

    public function indexAction()
    {
        $this->tag->setTitle("Help Index");
    }

    public function karmaAction()
    {
        $this->tag->setTitle("Karma & Reputation");
    }

    public function markdownAction()
    {
        $this->tag->setTitle("Markdown");
    }

    public function votingAction()
    {
        $this->tag->setTitle("Feedback System");
    }

    public function moderatorsAction()
    {
        $this->tag->setTitle("Moderation");
    }

    public function aboutAction()
    {
        $this->tag->setTitle("About {$this->config->site->name}");
    }

    public function createAction()
    {
        $this->tag->setTitle("Creating Posts");
    }

    public function badgesAction()
    {
        $this->tag->setTitle("Badges");

        $this->view->setVar('badges', (new BadgeManager)->getBadges());
    }

    public function statsAction()
    {
        $this->tag->setTitle("Statistics");

        $this->view->setVars([
            'threads'       => Posts::count(),
            'replies'       => Posts::sum(['column' => 'number_replies']),
            'votes'         => Posts::sum(['column' => 'votes_up + votes_down']),
            'users'         => Users::count(),
            'karma'         => Users::sum(['column' => 'karma']),
            'notifications' => Notifications::count(),
            'unotifications'=> ActivityNotifications::count(),
            'views'         => Posts::sum(['column' => 'number_views']),
            'irc'           => IrcLog::count(),
        ]);
    }
}
