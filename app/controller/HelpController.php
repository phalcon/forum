<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Controller;

use Phosphorum\Model\Posts;
use Phosphorum\Model\Users;
use Phosphorum\Model\IrcLog;
use Phosphorum\Model\Notifications;
use Phosphorum\Model\ActivityNotifications;
use Phosphorum\Badges\Manager as BadgeManager;
use Phosphorum\Model\Services\Service\Activities;

/**
 * Class HelpController
 *
 * @package Phosphorum\Controller
 */
class HelpController extends ControllerBase
{
    public function indexAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help', ['linked' => false]);

        $this->tag->setTitle("Help Index");
    }

    public function karmaAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Karma', '/help/karma', ['linked' => false]);

        $this->tag->setTitle("Karma & Reputation");
    }

    public function markdownAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Markdown', '/help/markdown', ['linked' => false]);

        $this->tag->setTitle("Markdown");
    }

    public function votingAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Voting', '/help/voting', ['linked' => false]);

        $this->tag->setTitle("Feedback System");
    }

    public function moderatorsAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Moderation', '/help/moderators', ['linked' => false]);

        $this->tag->setTitle("Moderation");
    }

    public function aboutAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('About', '/help/about', ['linked' => false]);

        $this->tag->setTitle("About {$this->config->site->name}");
    }

    public function createAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Posts', '/help/create-post', ['linked' => false]);

        $this->tag->setTitle("Creating Posts");
    }

    public function badgesAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Badges', '/help/badges', ['linked' => false]);

        $this->tag->setTitle("Badges");

        $this->view->setVar('badges', (new BadgeManager)->getBadges());
    }

    public function statsAction()
    {
        $this->breadcrumbs
            ->add('Help', '/help')
            ->add('Statistics', '/help/stats', ['linked' => false]);

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
            'activities'    => container(Activities::class)->getMostActiveUsers(),
        ]);
    }
}
