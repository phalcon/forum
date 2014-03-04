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

use Phosphorum\Models\Users,
	Phosphorum\Models\Posts,
	Phosphorum\Models\PostsReplies,
	Phosphorum\Models\PostsBounties,
	Phosphorum\Models\PostsRepliesHistory,
	Phosphorum\Models\PostsRepliesVotes,
	Phosphorum\Models\Karma,
	Phalcon\Http\Response;

class RepliesController extends \Phalcon\Mvc\Controller
{

	public function initialize()
	{
		$this->view->disable();
	}

	/**
	 * Returs the raw comment as it as edited
	 *
	 * @param int $id
	 */
	public function getAction($id)
	{

		$response = new Response();

		$usersId = $this->session->get('identity');
		if (!$usersId) {
			$response->setStatusCode('401', 'Unauthorized');
			return $response;
		}

		$postReply = PostsReplies::findFirst(array(
			'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
			'bind' => array($id, $usersId, $this->session->get('identity-moderator'))
		));
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

		$usersId = $this->session->get('identity');
		if (!$usersId) {
			return $this->response->redirect();
		}

		if (!$this->request->isPost()) {
			return $this->response->redirect();
		}

		$postReply = PostsReplies::findFirst(array(
			'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
			'bind' => array($this->request->getPost('id'), $usersId, $this->session->get('identity-moderator'))
		));
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

		return $this->response->redirect('discussion/' . $postReply->post->id . '/' . $postReply->post->slug . '#C' . $postReply->id);
	}

	/**
	 * Deletes a reply
	 *
	 * @param int $id
	 */
	public function deleteAction($id)
	{

		$usersId = $this->session->get('identity');
		if (!$usersId) {
			return $this->response->setStatusCode('401', 'Unauthorized');
		}

		$postReply = PostsReplies::findFirst(array(
			'id = ?0 AND (users_id = ?1 OR "Y" = ?2)',
			'bind' => array($id, $usersId, $this->session->get('identity-moderator'))
		));
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


			return $this->response->redirect('discussion/' . $postReply->post->id . '/' . $postReply->post->slug);
		}

		return $this->response->redirect();
	}

	/**
	 * Votes a post up
	 */
	public function voteUpAction($id = 0)
	{
		$response = new Response();

		/**
		 * Find the post using get
		 */
		$postReply = PostsReplies::findFirstById($id);
		if (!$postReply) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'Post reply does not exist'
			));
		}

		$user = Users::findFirstById($this->session->get('identity'));
		if (!$user) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You must log in first to vote'
			));
		}

		if ($user->votes <= 0) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You don\'t have enough votes available'
			));
		}

		$voted = PostsRepliesVotes::count(array(
			'posts_replies_id = ?0 AND users_id = ?1',
			'bind' => array($postReply->id, $user->id)
		));
		if ($voted) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You have already voted this reply'
			));
		}

		$postReplyVote = new PostsRepliesVotes();
		$postReplyVote->posts_replies_id = $postReply->id;
		$postReplyVote->users_id = $user->id;
		if (!$postReplyVote->save()) {
			foreach ($postReplyVote->getMessages() as $message) {
				return $response->setJsonContent(array(
					'status' => 'error',
					'message' => $message->getMessage()
				));
			}
		}

		$postReply->votes_up++;
		if ($postReply->users_id != $user->id) {
			if ($postReply->post->users_id == $user->id) {
				$points = (Karma::VOTE_UP_ON_MY_REPLY_ON_MY_POST + intval(abs($user->karma - $postReply->user->karma)/1000));
			} else {
				$points = (Karma::VOTE_UP_ON_MY_REPLY + intval(abs($user->karma - $postReply->user->karma)/1000));
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
					return $response->setJsonContent(array(
						'status' => 'error',
						'message' => $message->getMessage()
					));
				}
			}
		}

		return $response->setJsonContent(array(
			'status' => 'OK'
		));
	}

	/**
	 * Votes a post down
	 */
	public function voteDownAction($id = 0)
	{
		$response = new Response();

		/**
		 * Find the post using get
		 */
		$postReply = PostsReplies::findFirstById($id);
		if (!$postReply) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'Post reply does not exist'
			));
		}

		$user = Users::findFirstById($this->session->get('identity'));
		if (!$user) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You must log in first to vote'
			));
		}

		if ($user->votes <= 0) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You don\'t have enough votes available'
			));
		}

		$voted = PostsRepliesVotes::count(array(
			'posts_replies_id = ?0 AND users_id = ?1',
			'bind' => array($postReply->id, $user->id)
		));
		if ($voted) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You have already voted this reply'
			));
		}

		$postReplyVote = new PostsRepliesVotes();
		$postReplyVote->posts_replies_id = $postReply->id;
		$postReplyVote->users_id = $user->id;
		if (!$postReplyVote->save()) {
			foreach ($postReplyVote->getMessages() as $message) {
				return $response->setJsonContent(array(
					'status' => 'error',
					'message' => $message->getMessage()
				));
			}
		}

		$postReply->votes_down++;
		if ($postReply->users_id != $user->id) {
			if ($postReply->post->users_id == $user->id) {
				$points = (Karma::VOTE_UP_ON_MY_REPLY_ON_MY_POST + intval(abs($user->karma - $postReply->user->karma)/1000));
			} else {
				$points = (Karma::VOTE_UP_ON_MY_REPLY + intval(abs($user->karma - $postReply->user->karma)/1000));
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
					return $response->setJsonContent(array(
						'status' => 'error',
						'message' => $message->getMessage()
					));
				}
			}
		}

		return $response->setJsonContent(array(
			'status' => 'OK'
		));
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

		$first = true;
		$postHistories = PostsRepliesHistory::find(array(
			'posts_replies_id = ?0',
			'bind' => array($postReply->id),
			'order' => 'created_at DESC'
		));
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

			$diff = new \Diff($b, $a, array());
			$renderer = new \Diff_Renderer_Html_SideBySide();

			echo $diff->Render($renderer);
		} else {
			$this->flash->notice('No history available to show');
		}
	}

	/**
	 * Accepts a reply as answer
	 */
	public function acceptAction($id = 0)
	{
		$response = new Response();

		/**
		 * Find the post using get
		 */
		$postReply = PostsReplies::findFirstById($id);
		if (!$postReply) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'Post reply does not exist'
			));
		}

		$user = Users::findFirstById($this->session->get('identity'));
		if (!$user) {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You must log in first to vote'
			));
		}

		if ($postReply->accepted == 'Y') {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'This reply is already accepted as answer'
			));
		}

		if ($postReply->post->accepted_answer == 'Y') {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'This post already has an accepted answer'
			));
		}

		if ($postReply->post->users_id != $user->id && $user->moderator != 'Y') {
			return $response->setJsonContent(array(
				'status' => 'error',
				'message' => 'You can\'t accept this answer as correct'
			));
		}

		if ($postReply->post->users_id != $postReply->users_id) {

			$postReply->post->user->karma += 10;
			$postReply->post->user->votes_points += 10;

			$points = (30 + intval(abs($user->karma - $post->user->karma)/1000));

			$postBounty = PostsBounties::findFirst(array(
				'users_id = ?0 AND posts_replies_id = ?1',
				'bind' => array($postReply->users_id, $postReply->id)
			));
			if ($postBounty) {
				$points += $postBounty->points;
			}

			$postReply->user->karma += $points;
			$postReply->user->votes_points += $points;

			if ($postReply->users_id != $user->id && $postReply->post->users_id != $user->id) {
				$user->karma += 10;
				$user->votes_points += 10;
			}
		}

		$postReply->accepted = 'Y';
		$postReply->post->accepted_answer = 'Y';

		if ($postReply->save()) {

			if (!$user->save()) {
				foreach ($user->getMessages() as $message) {
					return $response->setJsonContent(array(
						'status' => 'error',
						'message' => $message->getMessage()
					));
				}
			}
		}

		return $response->setJsonContent(array(
			'status' => 'OK'
		));
	}


}