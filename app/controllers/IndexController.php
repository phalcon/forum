<?php

namespace Phosphorum\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class IndexController
 *
 * @package Phosphorum\Controllers
 */
class IndexController extends Controller
{

    /**
     * @return \Phalcon\Http\ResponseInterface
     */
    public function indexAction()
    {
        $this->flashSession->error('Page not found: ' . $this->escaper->escapeHtml($this->router->getRewriteUri()));
        return $this->response->redirect('discussions');
    }
}
