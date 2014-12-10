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

use Phalcon\Mvc\Controller;
use Phosphorum\Models\Users;
use Phosphorum\Models\PostsReplies;
use Phosphorum\Models\PostsBounties;
use Phosphorum\Models\PostsRepliesHistory;
use Phosphorum\Models\PostsRepliesVotes;
use Phosphorum\Models\ActivityNotifications;
use Phosphorum\Models\Karma;
use Phalcon\Http\Response;

/**
 * Class RepliesController
 *
 * @package Phosphorum\Controllers
 */
class RepliesController extends ControllerBase
{

    public function initialize()
    {
        $this->view->disable();
    }

    /**
     * Returs the raw comment as it as edited
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {

        $response = new Response();

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $response->setStatusCode('401', 'Unauthorized');
            return $response;
        }

        $parametersReply = array(
            'id = ?0',
            'bind' => array($id)
        );
        $postReply = PostsReplies::findFirst($parametersReply);
        if ($postReply) {
            $data = array('status' => 'OK', 'id' => $postReply->id, 'comment' => $postReply->content);
        } else {
            $data = array('status' => 'ERROR');
        }

        $response->setJsonContent($data);
        return $response;
    }

    /**
     * Updates a reply
     */
    public function updateAction()
    {
        if (!$this->security->checkToken()) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            return $this->response->redirect();
        }

        if (!$this->request->isPost()) {
            return $this->response->redirect();
        }

        $parametersReply = array(
            'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
            'bind' => array(
                $this->request->getPost('id'),
                $usersId,
                $this->session->get('identity-moderator')
            )
        );
        $postReply = PostsReplies::findFirst($parametersReply);
        if (!$postReply) {
            return $this->response->redirect();
        }

        $content = $this->request->getPost('content');
        if (trim($content)) {
            $postReply->content   = $content;
            $postReply->edited_at = time();
            if ($postReply->save()) {
                if ($usersId != $postReply->users_id) {
                    $user = Users::findFirstById($usersId);
                    if ($user) {
                        if ($user->moderator == 'Y') {
                            $user->increaseKarma(Karma::MODERATE_REPLY);
                            $user->save();
                        }
                    }
                }
            }
        }

        $href = 'discussion/' . $postReply->post->id . '/' . $postReply->post->slug . '#C' . $postReply->id;
        return $this->response->redirect($href);
    }

    /**
     * Deletes a reply
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');
        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            return $this->response->setStatusCode('401', 'Unauthorized');
        }

        $parametersReply = array(
            'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
            'bind' => array($id, $usersId, $this->session->get('identity-moderator'))
        );
        $postReply = PostsReplies::findFirst($parametersReply);
        if ($postReply) {

            if ($usersId == $postReply->users_id) {
                $user = $postReply->user;
                if ($user) {
                    $user->decreaseKarma(Karma::DELETE_REPLY_ON_SOMEONE_ELSE_POST);
                    $user->save();
                }
            } else {
                $user = Users::findFirstById($usersId);
                if ($user) {
                    if ($user->moderator == 'Y') {
                        $user->increaseKarma(Karma::MODERATE_DELETE_REPLY);
                        $user->save();
                    }
                }
            }

            if ($postReply->delete()) {
                if ($usersId != $postReply->post->users_id) {
                    $user = $postReply->post->user;
                    if ($user) {
                        $user->decreaseKarma(Karma::SOMEONE_DELETED_HIS_OR_HER_REPLY_ON_MY_POST);
                        $user->save();
                    }
                    $postReply->post->number_replies--;
                    $postReply->post->save();
                }

                $this->flashSession->success('Reply was deleted successfully');
            }

            $href = 'discussion/' . $postReply->post->id . '/' . $postReply->post->slug;
            return $this->response->redirect($href);
        }

        return $this->response->redirect();
    }

    protected function checkTokenGetJson()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');
        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            return false;
        }
        return true;
    }

    /**
     * Votes a post up
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = array(
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            );
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentLogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }

        $post = $postReply->post;
        if (!$post) {
            $contentPostNotExist = array(
                'status'  => 'error',
                'message' => 'Post associated to the reply does not exist'
            );
            return $response->setJsonContent($contentPostNotExist);
        }

        if ($post->deleted) {
            $contentDeleted = array(
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            );
            return $response->setJsonContent($contentDeleted);
        }

        $parametersVoted = array(
            'posts_replies_id = ?0 AND users_id = ?1',
            'bind' => array($postReply->id, $user->id)
        );
        $voted = PostsRepliesVotes::count($parametersVoted);
        if ($voted) {
            $contentAleadyVoted = array(
                'status'  => 'error',
                'message' => 'You have already voted this reply'
            );
            return $response->setJsonContent($contentAleadyVoted);
        }

        $postReplyVote                   = new PostsRepliesVotes();
        $postReplyVote->posts_replies_id = $postReply->id;
        $postReplyVote->users_id         = $user->id;
        $postReplyVote->vote             = PostsRepliesVotes::VOTE_UP;
        if (!$postReplyVote->save()) {
            foreach ($postReplyVote->getMessages() as $message) {
                $contentError = array(
                    'status'  => 'error',
                    'message' => $message->getMessage()
                );
                return $response->setJsonContent($contentError);
            }
        }

        $postReply->votes_up++;
        if ($postReply->users_id != $user->id) {
            if ($postReply->post->users_id == $user->id) {
                $karmaCount     = intval(abs($user->karma - $postReply->user->karma) / 1000);
                $points = Karma::VOTE_UP_ON_MY_REPLY_ON_MY_POST + $karmaCount;
            } else {
                $points = (Karma::VOTE_UP_ON_MY_REPLY + intval(abs($user->karma - $postReply->user->karma) / 1000));
            }
            $postReply->user->increaseKarma($points);
        }

        if ($postReply->save()) {

            if ($postReply->users_id != $user->id) {
                $user->increaseKarma(Karma::VOTE_UP_ON_SOMEONE_ELSE_REPLY);
            }
            $user->votes--;

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentError = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentError);
                }
            }
        }

        if ($user->id != $postReply->users_id) {
            $activity                       = new ActivityNotifications();
            $activity->users_id             = $postReply->users_id;
            $activity->posts_id             = $post->id;
            $activity->posts_replies_id     = $postReply->id;
            $activity->users_origin_id      = $user->id;
            $activity->type                 = 'R';
            $activity->save();
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Votes a post down
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = array(
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            );
            return $response->setJsonContent($csrfTokenError);
        }


        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentLogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }

        $post = $postReply->post;
        if (!$post) {
            $contentPostNotExist = array(
                'status'  => 'error',
                'message' => 'Post associated to the reply does not exist'
            );
            return $response->setJsonContent($contentPostNotExist);
        }

        if ($post->deleted) {
            $contentDeleted = array(
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            );
            return $response->setJsonContent($contentDeleted);
        }

        $parametersVoted = array(
            'posts_replies_id = ?0 AND users_id = ?1',
            'bind' => array($postReply->id, $user->id)
        );
        $voted = PostsRepliesVotes::count($parametersVoted);
        if ($voted) {
            $contentAleadyVoted = array(
                'status'  => 'error',
                'message' => 'You have already voted this reply'
            );
            return $response->setJsonContent($contentAleadyVoted);
        }

        $postReplyVote                   = new PostsRepliesVotes();
        $postReplyVote->posts_replies_id = $postReply->id;
        $postReplyVote->users_id         = $user->id;
        $postReplyVote->vote             = PostsRepliesVotes::VOTE_DOWN;
        if (!$postReplyVote->save()) {
            foreach ($postReplyVote->getMessages() as $message) {
                $contentError = array(
                    'status'  => 'error',
                    'message' => $message->getMessage()
                );
                return $response->setJsonContent($contentError);
            }
        }

        $postReply->votes_down++;
        if ($postReply->users_id != $user->id) {
            if ($postReply->post->users_id == $user->id) {
                $karmaCount = intval(abs($user->karma - $postReply->user->karma) / 1000);
                $points = (Karma::VOTE_DOWN_ON_MY_REPLY_ON_MY_POST + $karmaCount);
            } else {
                $points = (Karma::VOTE_DOWN_ON_MY_REPLY + intval(abs($user->karma - $postReply->user->karma) / 1000));
            }
            $postReply->user->decreaseKarma($points);
        }

        if ($postReply->save()) {

            if ($postReply->users_id != $user->id) {
                $user->decreaseKarma(Karma::VOTE_DOWN_ON_SOMEONE_ELSE_REPLY);
            }
            $user->votes--;

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentError = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentError);
                }
            }
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Shows the latest modification made to a post
     */
    public function historyAction($id = 0)
    {

        $this->view->disable();

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $this->flashSession->error('The reply does not exist');
            return $this->response->redirect();
        }

        $a = explode("\n", $postReply->content);

        $first         = true;
        $parametersHistory = array(
            'posts_replies_id = ?0',
            'bind'  => array($postReply->id),
            'order' => 'created_at DESC'
        );

        $postHistories = PostsRepliesHistory::find($parametersHistory);

        if (count($postHistories) > 1) {

            foreach ($postHistories as $postHistory) {
                if ($first) {
                    if ($postHistory->content != $postReply->content) {
                        $first = false;
                    }
                    continue;
                }
                break;
            }
        } else {
            $postHistory = $postHistories->getFirst();
        }

        if (is_object($postHistory)) {

            $b = explode("\n", $postHistory->content);

            $diff     = new \Diff($b, $a, array());
            $renderer = new \Diff_Renderer_Html_SideBySide();

            echo $diff->Render($renderer);
        } else {
            $this->flash->notice('No history available to show');
        }
    }

    /**
     * Accepts a reply as correct answer
     */
    public function acceptAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = array(
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            );
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentLogIn);
        }

        if ($postReply->accepted == 'Y') {
            $contentAlready = array(
                'status'  => 'error',
                'message' => 'This reply is already accepted as answer'
            );
            return $response->setJsonContent($contentAlready);
        }

        if ($postReply->post->deleted) {
            $contentDeleted = array(
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            );
            return $response->setJsonContent($contentDeleted);
        }

        if ($postReply->post->accepted_answer == 'Y') {
            $contentAlreadyAnswer = array(
                'status'  => 'error',
                'message' => 'This post already has an accepted answer'
            );
            return $response->setJsonContent($contentAlreadyAnswer);
        }

        if ($postReply->post->users_id != $user->id && $user->moderator != 'Y') {
            $contentCorrect = array(
                'status'  => 'error',
                'message' => 'You can\'t accept this answer as correct'
            );
            return $response->setJsonContent($contentCorrect);
        }

        if ($postReply->post->users_id != $postReply->users_id) {

            $postReply->post->user->karma += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;
            $postReply->post->user->votes_points += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;

            $points = (30 + intval(abs($user->karma - $postReply->user->karma) / 1000));

            $parametersBounty = array(
                'users_id = ?0 AND posts_replies_id = ?1',
                'bind' => array($postReply->users_id, $postReply->id)
            );
            $postBounty = PostsBounties::findFirst($parametersBounty);

            if ($postBounty) {
                $points += $postBounty->points;
            }

            $postReply->user->karma += $points;
            $postReply->user->votes_points += $points;

            if ($postReply->users_id != $user->id && $postReply->post->users_id != $user->id) {
                $user->karma += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;
                $user->votes_points += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;
            }
        }

        $postReply->accepted              = 'Y';
        $postReply->post->accepted_answer = 'Y';

        if ($postReply->save()) {

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentError = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentError);
                }
            }
        }

        if ($user->id != $postReply->users_id) {
            $activity                       = new ActivityNotifications();
            $activity->users_id             = $postReply->users_id;
            $activity->posts_id             = $postReply->post->id;
            $activity->posts_replies_id     = $postReply->id;
            $activity->users_origin_id      = $user->id;
            $activity->type                 = 'A';
            $activity->save();
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }
}
