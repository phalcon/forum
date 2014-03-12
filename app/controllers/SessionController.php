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

use Phosphorum\Github\OAuth,
	Phosphorum\Github\Users as GithubUsers,
	Phosphorum\Models\Users as ForumUsers,
	Phosphorum\Models\NotificationsBounces,
	Phosphorum\Models\Karma,
	Phalcon\Mvc\Controller,
	Phalcon\Mvc\Model;

class SessionController extends Controller
{

	protected function indexRedirect()
	{
		return $this->response->redirect('discussions');
	}

    /**
     * Returns to the discussion
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    protected function discussionsRedirect()
    {
        $referer =  $this->request->getHTTPReferer();
        $path = parse_url($referer, PHP_URL_PATH);
        if ($path) {
        	$this->router->handle($path);
        	return $this->router->wasMatched() ? $this->response->redirect($path, true) : $this->indexRedirect();
		} else {
			return $this->indexRedirect();
		}
    }

    public function authorizeAction()
    {

    	if (!$this->session->get('identity')) {
    		$oauth = new OAuth($this->config->github);
    		return $oauth->authorize();
    	}

    	return $this->discussionsRedirect();
    }

    public function accessTokenAction()
    {
    	$oauth = new OAuth($this->config->github);

    	$response = $oauth->accessToken();
    	if (is_array($response)) {

			if (isset($response['error'])) {
				$this->flashSession->error('Github: ' . $response['error']);
				return $this->indexRedirect();
			}

			$githubUser = new GithubUsers($response['access_token']);

			if (!$githubUser->isValid()) {
				$this->flashSession->error('Invalid Github response. Please try again');
				return $this->indexRedirect();
			}

			/**
			 * Edit/Create the user
			 */
			$user = ForumUsers::findFirstByAccessToken($response['access_token']);
			if ($user == false) {
				$user = new ForumUsers();
				$user->token_type = $response['token_type'];
				$user->access_token = $response['access_token'];
			}

			/**
			 * Update the user information
			 */
			$user->name = $githubUser->getName();
			$user->login = $githubUser->getLogin();
			$email = $githubUser->getEmail();
			if (is_string($email)) {
				$user->email = $email;
			} else {
				if (is_array($email)) {
					$user->email = $email[0];
				}
			}
			$user->gravatar_id = $githubUser->getGravatarId();
			$user->increaseKarma(Karma::LOGIN);

			if (!$user->save()) {
				foreach ($user->getMessages() as $message) {
					$this->flashSession->error((string) $message);
					return $this->indexRedirect();
				}
			}

			/**
			 * Store the user data in session
			 */
			$this->session->set('identity', $user->id);
			$this->session->set('identity-name', $user->name);
			$this->session->set('identity-gravatar', $user->gravatar_id);
			$this->session->set('identity-timezone', $user->timezone);
			$this->session->set('identity-moderator', $user->moderator);

			if ($user->getOperationMade() == Model::OP_CREATE) {
				$this->flashSession->success('Welcome ' . $user->name);
			} else {
				$this->flashSession->success('Welcome back ' . $user->name);
			}

			if (strpos($user->email, '@users.noreply.github.com') !== false) {
				$this->flashSession->notice('Your current e-mail: ' . $this->escaper->escapeHtml($user->email) . ' does not allow us to send you e-mail notifications');
			}

			if ($user->getOperationMade() != Model::OP_CREATE) {

				$bounces = NotificationsBounces::find(array(
					'email = ?0 AND reported = "N"',
					'bind' => array($user->email)
				));
				if (count($bounces)) {

					foreach ($bounces as $bounce) {
						$bounce->reported = 'Y';
						$bounce->save();
					}

					$this->flashSession->notice('We have failed to deliver you some email notifications,
						this might be caused by an invalid email associated to your Github account or
						its mail server is rejecting our emails. Your current e-mail is: ' . $this->escaper->escapeHtml($user->email));

					$bounces = NotificationsBounces::find(array(
						'email = ?0 AND created_at >= ?1',
						'bind' => array($user->email, time() - 86400 * 7)
					));
					if (count($bounces) >= NotificationsBounces::MAX_BOUNCES) {
						$this->flashSession->notice('Due to a repeated number of email bounces we have disabled email
							notifications for your email. You can renable them in your settings');
						$user->notifications = 'N';
						$user->save();
					}

				}
			}

			return $this->discussionsRedirect();
		}

		$this->flashSession->error('Invalid Github response. Please try again');
		return $this->discussionsRedirect();
    }

    public function logoutAction()
    {
    	$this->session->remove('identity');
    	$this->session->remove('identity-name');
    	$this->session->remove('identity-moderator');

    	$this->flashSession->success('Goodbye!');
		return $this->discussionsRedirect();
    }

}
