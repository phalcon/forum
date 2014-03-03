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

class UtilsController extends \Phalcon\Mvc\Controller
{

	public function initialize()
	{
		$this->view->disable();
	}

	public function karmaAction()
	{
		foreach (Users::find() as $user) {
			if ($user->karma === null) {
				$numberPosts = Posts::count(array(
					'users_id = ?0',
					'bind' => array($user->id)
				));

				$numberReplies = PostsReplies::count(array(
					'users_id = ?0',
					'bind' => array($user->id)
				));

				$user->karma = ($numberReplies * 10 + $numberPosts * 5);
				$user->votes = intval($user->karma / 50);
				$user->save();
			}
		}
	}

	public function previewAction()
	{
		$response = new Response();
		if ($this->request->isPost()) {
			if ($this->session->get('identity')) {
				$content = $this->request->getPost('content');
				$response->setContent($this->markdown->render($this->escaper->escapeHtml($content)));
			}
		}
		return $response;
	}

}

