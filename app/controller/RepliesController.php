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

use Phalcon\Mvc\View;
use Phalcon\Http\Response;
use Phosphorum\Model\Karma;
use Phosphorum\Model\Users;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Model\PostsBounties;
use Phosphorum\Model\PostsRepliesVotes;
use Phosphorum\Mvc\Controller\TokenTrait;
use Phosphorum\Model\ActivityNotifications;

/**
 * Class RepliesController
 *
 * @package Phosphorum\Controller
 */
class RepliesController extends ControllerBase
{
    use TokenTrait;

    public function initialize()
    {
        $this->view->disable();
    }

    /**
     * Returns the raw comment as it as edited
     *
     * @param $id
     * @return Response
     */
    public function getAction($id)
    {
        $response = new Response();

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $response->setStatusCode(401, 'Unauthorized');
            return $response;
        }

        $parametersReply = [
            'id = ?0',
            'bind' => [$id]
        ];
        $postReply = PostsReplies::findFirst($parametersReply);
        if ($postReply) {
            $data = ['status' => 'OK', 'id' => $postReply->id, 'comment' => $postReply->content];
        } else {
            $data = ['status' => 'ERROR'];
        }

        $response->setJsonContent($data);
        return $response;
    }

    /**
     * Updates a reply
     */
    public function updateAction()
    {
        $usersId = $this->session->get('identity');
        if (!$usersId) {
            return $this->response->redirect();
        }

        if (!$this->request->isPost()) {
            return $this->response->redirect();
        }

        $parametersReply = [
            'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
            'bind' => [
                $this->request->getPost('id'),
                $usersId,
                $this->session->get('identity-moderator')
            ]
        ];
        $postReply = PostsReplies::findFirst($parametersReply);
        if (!$postReply) {
            return $this->response->redirect();
        }

        if (!$this->checkTokenPost('post-' . $postReply->post->id)) {
            $this->flashSession->error('This post is outdated. Please try to update reply again.');
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
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $usersId = $this->session->get('identity');
        if (!$usersId) {
            return $this->response->setStatusCode('401', 'Unauthorized');
        }

        $parametersReply = [
            'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
            'bind' => [$id, $usersId, $this->session->get('identity-moderator')]
        ];

        $postReply = PostsReplies::findFirst($parametersReply);
        if (!$postReply) {
            $this->flashSession->error('Post reply does not exist');
            return $this->response->redirect();
        }

        if (!$this->checkTokenGetJson('post-' . $postReply->post->id)) {
            $this->flashSession->error('This post is outdated. Please try to vote for the reply again.');
            return $this->response->redirect();
        }

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

    /**
     * Votes a post up
     *
     * @param int $id
     * @return Response
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        if (!$this->checkTokenGetJson('post-' . $postReply->post->id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to vote for the reply again.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentLogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = [
                'status'  => 'error',
                'message' => "You don't have enough votes available"
            ];
            return $response->setJsonContent($contentDontHave);
        }

        $post = $postReply->post;
        if (!$post) {
            $contentPostNotExist = [
                'status'  => 'error',
                'message' => 'Post associated to the reply does not exist'
            ];
            return $response->setJsonContent($contentPostNotExist);
        }

        if ($post->deleted) {
            $contentDeleted = [
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            ];
            return $response->setJsonContent($contentDeleted);
        }

        $parametersVoted = [
            'posts_replies_id = ?0 AND users_id = ?1',
            'bind' => [$postReply->id, $user->id]
        ];
        $voted = PostsRepliesVotes::count($parametersVoted);
        if ($voted) {
            $contentAlreadyVoted = [
                'status'  => 'error',
                'message' => 'You have already voted this reply'
            ];
            return $response->setJsonContent($contentAlreadyVoted);
        }

        $postReplyVote                   = new PostsRepliesVotes();
        $postReplyVote->posts_replies_id = $postReply->id;
        $postReplyVote->users_id         = $user->id;
        $postReplyVote->vote             = PostsRepliesVotes::VOTE_UP;
        if (!$postReplyVote->save()) {
            foreach ($postReplyVote->getMessages() as $message) {
                $contentError = [
                    'status'  => 'error',
                    'message' => $message->getMessage()
                ];
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
                    $contentError = [
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    ];
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

        return $response->setJsonContent(['status' => 'OK']);
    }

    /**
     * Votes a post down
     *
     * @param int $id
     * @return Response
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        if (!$this->checkTokenGetJson('post-' . $postReply->post->id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to vote for the reply again.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentLogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = [
                'status'  => 'error',
                'message' => "You don't have enough votes available"
            ];
            return $response->setJsonContent($contentDontHave);
        }

        $post = $postReply->post;
        if (!$post) {
            $contentPostNotExist = [
                'status'  => 'error',
                'message' => 'Post associated to the reply does not exist'
            ];
            return $response->setJsonContent($contentPostNotExist);
        }

        if ($post->deleted) {
            $contentDeleted = [
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            ];
            return $response->setJsonContent($contentDeleted);
        }

        $parametersVoted = [
            'posts_replies_id = ?0 AND users_id = ?1',
            'bind' => [$postReply->id, $user->id]
        ];
        $voted = PostsRepliesVotes::count($parametersVoted);
        if ($voted) {
            $contentAlreadyVoted = [
                'status'  => 'error',
                'message' => 'You have already voted this reply'
            ];
            return $response->setJsonContent($contentAlreadyVoted);
        }

        $postReplyVote                   = new PostsRepliesVotes();
        $postReplyVote->posts_replies_id = $postReply->id;
        $postReplyVote->users_id         = $user->id;
        $postReplyVote->vote             = PostsRepliesVotes::VOTE_DOWN;
        if (!$postReplyVote->save()) {
            foreach ($postReplyVote->getMessages() as $message) {
                $contentError = [
                    'status'  => 'error',
                    'message' => $message->getMessage()
                ];
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
                    $contentError = [
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    ];
                    return $response->setJsonContent($contentError);
                }
            }
        }

        return $response->setJsonContent(['status' => 'OK']);
    }

    /**
     * Shows the latest modification made to a post reply
     *
     * @param int $id The PostsReplies id.
     */
    public function historyAction($id = 0)
    {
        $this->view->enable();
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        /**
         * Find the post reply using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $this->view->setVar('difference', 'The reply does not exist or it has been deleted.');
            return;
        }

        $this->view->setVar('difference', $postReply->getDifference() ?: 'No history available to show');
    }

    /**
     * Accepts a reply as correct answer
     *
     * @param int $id
     * @return Response
     */
    public function acceptAction($id = 0)
    {
        $response = new Response();

        /**
         * Find the post using get
         */
        $postReply = PostsReplies::findFirstById($id);
        if (!$postReply) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Post reply does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        if (!$this->checkTokenGetJson('post-' . $postReply->post->id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to accept reply again.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentLogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentLogIn);
        }

        if ($postReply->accepted == 'Y') {
            $contentAlready = [
                'status'  => 'error',
                'message' => 'This reply is already accepted as answer'
            ];
            return $response->setJsonContent($contentAlready);
        }

        if ($postReply->post->deleted) {
            $contentDeleted = [
                'status'  => 'error',
                'message' => 'Post associated to the reply is deleted'
            ];
            return $response->setJsonContent($contentDeleted);
        }

        if ($postReply->post->accepted_answer == 'Y') {
            $contentAlreadyAnswer = [
                'status'  => 'error',
                'message' => 'This post already has an accepted answer'
            ];
            return $response->setJsonContent($contentAlreadyAnswer);
        }

        if ($postReply->post->users_id != $user->id && $user->moderator != 'Y') {
            $contentCorrect = [
                'status'  => 'error',
                'message' => "You can't accept this answer as correct"
            ];
            return $response->setJsonContent($contentCorrect);
        }

        if ($postReply->post->users_id != $postReply->users_id) {
            $postReply->post->user->karma += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;
            $postReply->post->user->votes_points += Karma::SOMEONE_ELSE_ACCEPT_YOUR_REPLY;

            $points = (30 + intval(abs($user->karma - $postReply->user->karma) / 1000));

            $parametersBounty = [
                'users_id = ?0 AND posts_replies_id = ?1',
                'bind' => [$postReply->users_id, $postReply->id]
            ];
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
                    $contentError = [
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    ];
                    return $response->setJsonContent($contentError);
                }
            }
        }

        if ($user->id != $postReply->users_id) {
            $activity = new ActivityNotifications([
                'users_id'         => $postReply->users_id,
                'posts_id'         => $postReply->post->id,
                'posts_replies_id' => $postReply->id,
                'users_origin_id'  => $user->id,
                'type'             => 'A',
            ]);

            $activity->save();
        }

        return $response->setJsonContent(['status' => 'OK']);
    }
}
