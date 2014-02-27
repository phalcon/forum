<?php

namespace Phosphorum\Controllers;

class IndexController extends \Phalcon\Mvc\Controller
{

	public function indexAction()
	{				
		$this->flashSession->error('Page not found: ' . $this->escaper->escapeHtml($this->router->getRewriteUri()));
		return $this->response->redirect('discussions');
	}
}
