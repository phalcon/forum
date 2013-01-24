<?php

namespace Phosphorum\Controllers;

class IndexController extends \Phalcon\Mvc\Controller
{

	public function indexAction()
	{
		$this->view->disable();
		$this->flashSession->error('Page not found');
		return $this->response->redirect();
	}
}
