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

use Phosphorum\Models\Users;
use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsReplies;
use Phosphorum\Models\Activities;
use Phosphorum\Mvc\Controllers\TokenTrait;

/**
 * Class UsersController
 *
 * @package Phosphorum\Controllers
 */
class UsersController extends ControllerBase
{
    use TokenTrait;

    public function initialize()
    {
        parent::initialize();

        $this->gravatar->setSize(220);
    }

    /**
     * Shows the user profile
     *
     * @param int    $id       User id
     * @param string $username User name
     */
    public function viewAction($id, $username)
    {
        $user = $id ? Users::findFirstById($id) : Users::findFirstByLogin($username);
        if (!$user) {
            $user = Users::findFirstByName($username);
        }

        if (!$user) {
            $this->flashSession->error('The user does not exist');
            $this->response->redirect();
            return;
        }

        $this->view->setVar('user', $user);

        $parametersNumberPosts = [
            'users_id = ?0 AND deleted = 0',
            'bind' => [$user->id]
        ];
        $this->view->setVar('numberPosts', Posts::count($parametersNumberPosts));

        $parametersNumberReplies = [
            'users_id = ?0',
            'bind' => [$user->id]
        ];
        $this->view->setVar('numberReplies', PostsReplies::count($parametersNumberReplies));

        $parametersActivities = [
            'users_id = ?0',
            'bind'  => [$user->id],
            'order' => 'created_at DESC',
            'limit' => 15
        ];
        $this->view->setVar('activities', Activities::find($parametersActivities));

        $users   = Users::find(['columns' => 'id', 'conditions' => 'karma != 0', 'order' => 'karma DESC']);
        $ranking = count($users);
        foreach ($users as $position => $everyUser) {
            if ($everyUser->id == $user->id) {
                $ranking = $position + 1;
                break;
            }
        }

        $this->view->setVars([
            'ranking'       => $ranking,
            'total_ranking' => count($users),
            'avatar'        => $this->gravatar->getAvatar($user->email),
        ]);

        $this->tag->setTitle('Profile - ' . $this->escaper->escapeHtml($user->name));
    }

    /**
     * Allow to change your user settings
     */
    public function settingsAction()
    {
        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            $this->response->redirect();
            return;
        }

        $user = Users::findFirstById($usersId);
        if (!$user) {
            $this->flashSession->error('The user does not exist');
            $this->response->redirect();
            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                $this->response->redirect();
                return;
            }

            $user->timezone      = $this->request->getPost('timezone');
            $user->notifications = $this->request->getPost('notifications');
            $user->theme         = $this->request->getPost('theme');
            $user->digest        = $this->request->getPost('digest');
            if ($user->save()) {
                $this->session->set('identity-theme', $user->theme);
                $this->session->get('identity-timezone', $user->timezone);
                $this->flashSession->success('Settings were successfully updated');
                $this->response->redirect();
                return;
            }
        } else {
            $this->tag->displayTo('timezone', $user->timezone);
            $this->tag->displayTo('notifications', $user->notifications);
            $this->tag->displayTo('theme', $user->theme);
            $this->tag->displayTo('digest', $user->digest);
        }

        $this->tag->setTitle('My Settings');
        $this->tag->setAutoescape(false);

        $this->view->setVars([
            'avatar'        => $this->gravatar->getAvatar($user->email),
            'user'          => $user,
            'subscribed'    => ($user->digest == 'Y'),
            'timezones'     => $this->di->getShared('timezones'),
            'numberPosts'   => Posts::count(['users_id = ?0 AND deleted = 0', 'bind' => [$user->id]]),
            'numberReplies' => PostsReplies::count(['users_id = ?0', 'bind' => [$user->id]]),
        ]);
    }
}
