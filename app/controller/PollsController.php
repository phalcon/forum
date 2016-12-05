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
use Phosphorum\Model\PostsPollOptions;
use Phosphorum\Model\PostsPollVotes;
use Phosphorum\Model\Users;
use Phosphorum\Model\Karma;
use Phosphorum\Mvc\Controller\TokenTrait;
use Phalcon\Http\Response;

/**
 * Polls Controller
 *
 * @package Phosphorum\Controller
 */
class PollsController extends ControllerBase
{
    use TokenTrait;

    /**
     * This initializes the timezone in each request
     */
    public function initialize()
    {
        if ($timezone = $this->session->get('identity-timezone')) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Votes for a poll option
     *
     * @param int $id     Post ID
     * @param int $option Option ID
     * @return Response
     */
    public function voteAction($id = 0, $option = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson('post-' . $id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to vote again.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        if (!$post = Posts::findFirstById($id)) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Poll does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        if (!$user = Users::findFirstById($this->session->get('identity'))) {
            $contentlogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentlogIn);
        }

        if (!$option = PostsPollOptions::findFirstById($option)) {
            $optionNotFound = [
                'status'  => 'error',
                'message' => 'Please select one option from the list below'
            ];
            return $response->setJsonContent($optionNotFound);
        }

        if ($post->isParticipatedInPoll($user->id)) {
            $contentAlreadyVote = [
                'status'  => 'error',
                'message' => 'You have already voted this post'
            ];
            return $response->setJsonContent($contentAlreadyVote);
        }

        $pollVote             = new PostsPollVotes();
        $pollVote->posts_id   = $post->id;
        $pollVote->users_id   = $user->id;
        $pollVote->options_id = $option->id;
        if (!$pollVote->save()) {
            foreach ($pollVote->getMessages() as $message) {
                /** @var \Phalcon\Mvc\Model\Message $message */
                $contentError = [
                    'status'  => 'error',
                    'message' => $message->getMessage()
                ];
                return $response->setJsonContent($contentError);
            }
        }

        if ($post->users_id != $user->id) {
            $post->user->increaseKarma(Karma::SOMEONE_DID_VOTE_MY_POLL);
            $user->increaseKarma(Karma::VOTE_ON_SOMEONE_ELSE_POLL);
        }

        if (!$post->save()) {
            foreach ($post->getMessages() as $message) {
                /** @var \Phalcon\Mvc\Model\Message $message */
                $contentErrorSave = [
                    'status'  => 'error',
                    'message' => $message->getMessage()
                ];
                return $response->setJsonContent($contentErrorSave);
            }
        }

        $viewCache = $this->getDI()->getShared('viewCache');
        $viewCache->delete('post-' . $post->id);
        $viewCache->delete('poll-votes-' . $post->id);
        $viewCache->delete('poll-options-' . $post->id);

        $contentOk = [
            'status' => 'OK'
        ];

        return $response->setJsonContent($contentOk);
    }
}
