<?php

namespace Phosphorum\Controllers;

use Phosphorum\Models\Users,
	Phosphorum\Models\Posts,
	Phosphorum\Models\PostsReplies,
	Phalcon\Http\Response;

class HelpController extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->view->setTemplateBefore(array('discussions'));
	}

	public function indexAction()
	{

	}

	public function karmaAction()
	{

	}

	public function markdownAction()
	{

	}

}

