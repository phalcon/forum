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

use Phosphorum\Github\OAuth;
use Phosphorum\Github\Users as GithubUsers;
use Phosphorum\Model\Users as ForumUsers;
use Phosphorum\Model\Karma;
use Phosphorum\Model\NotificationsBounces;
use Phalcon\Mvc\Model;
use Phalcon\Config;

/**
 * Class SessionController
 *
 * @package Phosphorum\Controller
 */
class SessionController extends ControllerBase
{
    /**
     * @return \Phalcon\Http\ResponseInterface
     */
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
        $referer = $this->request->getHTTPReferer();
        $path    = parse_url($referer, PHP_URL_PATH);
        if ($path) {
            $this->router->handle($path);
            return $this->router->wasMatched() ? $this->response->redirect($path, true) : $this->indexRedirect();
        } else {
            return $this->indexRedirect();
        }
    }

    /**
     * @return \Phalcon\Http\ResponseInterface|void
     */
    public function authorizeAction()
    {
        if (!$this->session->has('identity')) {
            $oauth = new OAuth($this->config->get('github', new Config));
            return $oauth->authorize();
        }

        return $this->discussionsRedirect();
    }

    /**
     * @return \Phalcon\Http\ResponseInterface
     */
    public function accessTokenAction()
    {
        $oauth = new OAuth($this->config->get('github', new Config));

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

            $userName = $githubUser->getLogin();
            if (empty($userName)) {
                $this->flashSession->error(
                    'Invalid Github response. Please try again'
                );
                return $this->indexRedirect();
            }

            /**
             * Edit/Create the user
             */
            $user = ForumUsers::findFirst(
                [
                    'conditions' => 'login = :login:',
                    'bind'       => [
                        'login' => $userName,
                    ],
                ]
            );
            if ($user == false) {
                $user               = new ForumUsers();
                $user->token_type   = $response['token_type'];
                $user->access_token = $response['access_token'];
            }

            if ($user->banned == 'Y') {
                $this->flashSession->error('You have been banned from the forum.');
                return $this->indexRedirect();
            }

            // Update session id
            $this->session->regenerateId(true);

            /**
             * Update the user information
             */
            $user->name  = $githubUser->getName();
            $user->login = $githubUser->getLogin();
            $email       = $githubUser->getEmail();

            if (is_string($email)) {
                $user->email = $email;
            } elseif (is_array($email) && isset($email['email'])) {
                $user->email = $email['email'];
            }

            // In any case user has Gravatar ID even if he has no email
            $user->gravatar_id = $this->gravatar->getEmailHash($user->email);

            $user->increaseKarma(Karma::LOGIN);

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $this->flashSession->error((string)$message);
                    return $this->indexRedirect();
                }
            }

            /**
             * Store the user data in session
             */
            $this->session->set('identity', $user->id);
            $this->session->set('identity-name', $user->name);
            $this->session->set('identity-email', $user->email);
            $this->session->set('identity-gravatar', $user->gravatar_id);
            $this->session->set('identity-timezone', $user->timezone);
            $this->session->set('identity-theme', $user->theme);
            $this->session->set('identity-moderator', $user->moderator);
            $this->session->set('identity-admin', $user->admin);
            $this->session->set('identity-karma', $user->karma);

            if ($user->getOperationMade() == Model::OP_CREATE) {
                $this->flashSession->success('Welcome ' . $user->name);
            } else {
                $this->flashSession->success('Welcome back ' . $user->name);
            }

            if ($user->email) {
                if (false !== strpos($user->email, '@users.noreply.github.com')) {
                    $messageNotAllow = sprintf(
                        'Your current e-mail %s does not allow us to send you e-mail notifications',
                        $this->escaper->escapeHtml($user->email)
                    );
                    $this->flashSession->notice($messageNotAllow);
                }
            } else {
                $messageCantSend = "We weren't able to obtain your e-mail address"
                    . " from Github, we can't send you e-mail notifications";
                $this->flashSession->notice($messageCantSend);
            }

            if ($user->getOperationMade() != Model::OP_CREATE) {
                /**
                 * Show a notification to users that have e-mail bounces
                 */
                $parametersBounces = [
                    'email = ?0 AND reported = "N"',
                    'bind' => [$user->email]
                ];
                $bounces = NotificationsBounces::find($parametersBounces);
                if (count($bounces)) {
                    foreach ($bounces as $bounce) {
                        $bounce->reported = 'Y';
                        $bounce->save();
                    }

                    $messageFailed
                        = 'We have failed to deliver you some email notifications,'
                        . ' this might be caused by an invalid email associated to your Github account or '
                        . 'its mail server is rejecting our emails. Your current e-mail is: '
                        . $this->escaper->escapeHtml($user->email);

                    $this->flashSession->notice($messageFailed);

                    $parametersBouncesMax = [
                        'email = ?0 AND created_at >= ?1',
                        'bind' => [$user->email, time() - 86400 * 7]
                    ];

                    $bounces = NotificationsBounces::find($parametersBouncesMax);

                    if (count($bounces) >= NotificationsBounces::MAX_BOUNCES) {
                        $messageRepeat
                            = 'Due to a repeated number of email bounces we have disabled email '
                            . 'notifications for your email. You can re-enable them in your settings';
                        $this->flashSession->notice($messageRepeat);
                        $user->notifications = 'N';
                        $user->save();
                    }
                }

                /**
                 * Show a notification to users that haven't spend their votes
                 */
                if ($user->votes >= 10 && mt_rand(1, 5) == 3) {
                    $this->flashSession->notice(
                        "You have {$user->votes} votes remaining to spend. " .
                        'If you find something useful in this forum do not hesitate to give others some votes.'
                    );
                }
            }

            return $this->discussionsRedirect();
        }

        $this->flashSession->error('Invalid Github response. Please try again');
        return $this->discussionsRedirect();
    }

    /**
     * @return \Phalcon\Http\ResponseInterface
     */
    public function logoutAction()
    {
        $this->session->remove('identity');
        $this->session->remove('identity-name');
        $this->session->remove('identity-moderator');
        $this->session->remove('identity-admin');
        $this->session->remove('identity-gravatar');
        $this->session->remove('identity-email');
        $this->session->remove('identity-theme');

        $this->flashSession->success('Goodbye!');
        return $this->discussionsRedirect();
    }
}
