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

use Phalcon\Mvc\Controller;
use Phosphorum\Models\Users;
use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsReplies;
use Phalcon\Http\Response;

/**
 * Class UtilsController
 *
 * @package Phosphorum\Controllers
 */
class UtilsController extends Controller
{

    public function initialize()
    {
        $this->view->disable();
    }

    public function karmaAction()
    {
        foreach (Users::find() as $user) {

            if ($user->karma === null) {

                $parametersNumbersPost = array(
                    'users_id = ?0',
                    'bind' => array($user->id)
                );
                $numberPosts = Posts::count($parametersNumbersPost);

                $parametersNumberReplies = array(
                    'users_id = ?0',
                    'bind' => array($user->id)
                );
                $numberReplies = PostsReplies::count($parametersNumberReplies);

                $user->karma = ($numberReplies * 10 + $numberPosts * 5);
                $user->votes = intval($user->karma / 50);
                $user->save();
            }
        }
    }

    /**
     * @return Response
     */
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
