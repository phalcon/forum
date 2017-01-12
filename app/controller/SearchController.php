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

use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Phosphorum\Model\Posts;
use Phosphorum\Search\Indexer;
use Phalcon\Http\ResponseInterface;

/**
 * Phosphorum\Controller\SearchController
 *
 * @package Phosphorum\Controller
 */
class SearchController extends ControllerBase
{
    /**
     * Perform the search of posts only searching in the title
     */
    public function indexAction()
    {
        $this->tag->setTitle('Search Results');

        $q = $this->request->getQuery('q');

        $indexer = new Indexer();

        $posts = $indexer->search(['title' => $q, 'content' => $q], 50, true);
        if (!count($posts)) {
            $posts = $indexer->search(['title' => $q], 50, true);
            if (!count($posts)) {
                $this->flashSession->notice('There are no search results');
                $this->response->redirect();
                return;
            }
        }

        $paginator = new \stdClass;
        $paginator->count = 0;

        $this->view->setVars([
            'posts'        => $posts,
            'totalPosts'   => $paginator,
            'currentOrder' => null,
            'offset'       => 0,
            'paginatorUri' => 'search'
        ]);
    }

    /**
     * Finds related posts
     *
     * @return ResponseInterface
     */
    public function findRelatedAction()
    {
        $response = new Response();
        $indexer  = new Indexer();
        $results  = [];

        if ($this->request->has('title')) {
            if ($title = $this->request->getPost('title', 'trim')) {
                $results = $indexer->search(['title' => $title], 5);
            }
        }

        $content = [
            'status'  => 'OK',
            'results' => $results
        ];

        return $response->setJsonContent($content);
    }

    /**
     * Finds related posts
     */
    public function showRelatedAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        if (!$post = Posts::findFirstById($this->request->getPost('id', 'int'))) {
            $this->view->setVar('posts', []);
            return;
        }

        $indexer = new Indexer();
        $posts = $indexer->search(
            [
                'title'    => $post->title,
                'category' => $post->categories_id
            ],
            5,
            true
        );

        if (count($posts) == 0) {
            $posts = $indexer->search(['title' => $post->title], 5, true);
        }

        $this->view->setVar('posts', $posts);
    }
}
