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

use Phalcon\Mvc\Controller;
use Phosphorum\Model\Users;
use Phosphorum\Model\Posts;
use Phosphorum\Model\PostsReplies;
use Phalcon\Http\Response;

/**
 * Class UtilsController
 *
 * @property \Ciconia\Ciconia markdown
 * @package Phosphorum\Controller
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
                $parametersNumbersPost = [
                    'users_id = ?0',
                    'bind' => [$user->id]
                ];
                $numberPosts = Posts::count($parametersNumbersPost);

                $parametersNumberReplies = [
                    'users_id = ?0',
                    'bind' => [$user->id]
                ];
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
