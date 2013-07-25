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
		return $this->response->redirect();
	}

    /**
     * Returns to the discussion
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    protected function discussionsRedirect()
    {

        $referer =  $this->request->getHTTPReferer();

        $path = parse_url($referer,PHP_URL_PATH);

        $this->router->handle($path);
        $matched = $this->router->wasMatched();

        return $matched ? $this->response->redirect($path,true) : $this->indexRedirect();
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
				$this->flashSession->error('Github: '.$response['error']);
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

			if ($user->getOperationMade() == Model::OP_CREATE) {
				$this->flashSession->success('Welcome '.$user->name);
			} else {
				$this->flashSession->success('Welcome back '.$user->name);
			}

			return $this->discussionsRedirect();
		}

		$this->flashSession->error('Invalid Github response');
		return $this->discussionsRedirect();
    }

    public function logoutAction()
    {
    	$this->session->remove('identity');

    	$this->flashSession->success('Goodbye!');
		return $this->discussionsRedirect();
    }

    public function shadowLoginAction()
    {
    	/**
		 * Store the user data in session
		 */
		$this->session->set('identity', 1);
		$this->session->set('identity-name', 'Phalcon');
		$this->session->set('identity-gravatar', '5d6f567f9109789fd9f702959768e35d');
		$this->session->set('identity-timezone', 'America/Bogota');
    }

}
