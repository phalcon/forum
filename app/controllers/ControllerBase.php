<?php

namespace Phosphorum\Controllers;

use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;

class ControllerBase extends \Phalcon\Mvc\Controller
{
	
        public function onConstruct()
        {
			$users = Users::find()->getLast();
			$this->view->setVar("threads", Posts::count());
			$this->view->setVar("users", Users::count());
			$this->view->setVar("users_latest", $users->login);
			$this->view->actionName = $this->dispatcher->getActionName();
        }
		
}