<?php

namespace Phosphorum\Controllers;

use Phosphorum\Github\OAuth,
	Phosphorum\Github\Users as GithubUsers,
	Phosphorum\Models\Users as ForumUsers,
	Phalcon\Mvc\Model;

class SessionController extends \Phalcon\Mvc\Controller
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
				$this->flashSession->error('Invalid Github response');
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
			$user->email = $githubUser->getEmail();
			$user->gravatar_id = $githubUser->getGravatarId();
			$user->karma += 5;
			$user->votes_points += 5;

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

			return $this->discussionsRedirect();
		}

		$this->flashSession->error('Invalid Github response');
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
