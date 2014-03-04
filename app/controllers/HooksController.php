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
	Phalcon\Http\Response;

class HooksController extends \Phalcon\Mvc\Controller
{

	public function initialize()
	{
		//$this->view->disable();
	}

	public function mailReplyAction()
	{

		$response = new Response();
		/*if ($this->request->isPost()) {
			file_put_contents('../a.txt', print_r($_POST, true));
		}*/

		$events = json_decode($data['mandrill_events'][0], true);

		foreach ($events as $event) {

			if (!isset($event['event'])) {
				continue;
			}

			$type = $event['event'];
			if ($type != 'inbound') {
				continue;
			}

			if (!isset($event['msg'])) {
				continue;
			}

			$msg = $event['msg'];
			if (!isset($msg['dkim'])) {
				continue;
			}

			$dkim = $msg['dkim'];
			if (!isset($dkim['signed']) || !isset($dkim['valid'])) {
				echo 'here';
				continue;
			}

			if (!$dkim['signed'] || !$dkim['valid']) {
				continue;
			}

			if (!isset($msg['from_email'])) {
				continue;
			}

			if (!isset($msg['email'])) {
				continue;
			}

			if (!isset($msg['text'])) {
				continue;
			}

			var_dump($msg['spam_report']);

			//$msg['from_email'] = 'andres@phalconphp.com';

			$user = Users::findFirstByEmail($msg['from_email']);
			if (!$user) {
				continue;
			}

			$email = $msg['email'];

			//$email = 'reply-1234@phosphorum.com';

			if (!preg_match('#^reply-([0-9]+)@phosphorum.com$#', $email, $matches)) {
				continue;
			}

			$post = Posts::findFirst($matches[1]);
			if (!$post) {
				continue;
			}

			$postReply = new PostsReplies();
			$postReply->post = $post;
			$postReply->users_id = $user->id;
			$postReply->content = $msg['text'];
			$postReply->save();

		}

		//var_dump($events);

		return $response;
	}

}

