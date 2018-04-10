<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2017 Phalcon Team and contributors                  |
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

use Phalcon\Mvc\View;
use Phalcon\Paginator\Pager;
use Phosphorum\Model\Categories;
use Phosphorum\Model\TopicTracking;
use Phosphorum\Mvc\Traits\TokenTrait;
use Phosphorum\Exception\HttpException;
use Phalcon\Paginator\Pager\Range\Sliding;
use Phalcon\Paginator\Pager\Layout\Bootstrap;
use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;

/**
 * Phosphorum\Controller\IndexController
 *
 * @package Phosphorum\Controller
 */
class CategoriesController extends ControllerBase
{

    use TokenTrait;

    /**
     * Shows latest posts by category.
     *
     * @param int    $categoryId The category id
     * @param string $slug       The category slug [Optional]
     * @param int    $offset     The posts offset [Optional]
     */
    public function viewAction($categoryId, $slug = null, $offset = 0)
    {
        if (!$category = Categories::findFirstById($categoryId)) {
            $this->flashSession->notice("The category doesn't exist");
            $this->response->redirect();

            return;
        }

        $this->tag->setTitle("Discussions in category {$category->name}");
        $readposts = [];

        if ($userId = $this->session->get('identity')) {
            $ur = TopicTracking::findFirst(['user_id= ?0', 'bind' => [$userId]]);
            $readposts = $ur ? explode(',', $ur->topic_id) : [];
        }

        /**
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder
         */
        list($itemBuilder, $totalBuilder) = $this->prepareQueries();

        if (($currentPage = abs($this->request->getQuery('page', 'int'))) == 0) {
            $currentPage = 1;
        }

        $posts = $itemBuilder
            ->where('p.categories_id = :cat_id: AND p.deleted = 0', ['cat_id' => $categoryId])
            ->orderBy('p.created_at DESC')
            ->offset((int)($currentPage - 1) * self::POSTS_IN_PAGE)
            ->leftJoin('Phosphorum\Model\PostsReplies', 'p.id = rp.posts_id', 'rp')
            ->groupBy('p.id')
            ->columns([
                'p.*',
                'COUNT(rp.posts_id) AS count_replies',
                'IFNULL(MAX(rp.modified_at), MAX(rp.created_at)) AS reply_time'
            ])
            ->getQuery()
            ->execute();

        if (!count($posts)) {
            $this->flashSession->notice('There are no posts in category: ' . $category->name);
            $this->response->redirect();

            return;
        }

        $totalBuilder->where("p.categories_id = :cat_id: AND p.deleted = 0", ['cat_id' => $categoryId]);
        $pager = new Pager(
            new Paginator([
                'builder' => $totalBuilder,
                'limit'   => self::POSTS_IN_PAGE,
                'page'    => $currentPage,
            ]),
            [
                'layoutClass' => Bootstrap::class,
                'rangeClass'  => Sliding::class,
                'rangeLength' => 10,
                'urlMask'     => sprintf('%s?page={%%page_number}', ""),
            ]
        );

        $totalPosts = $totalBuilder
            ->getQuery()
            ->setUniqueRow(true)
            ->execute();


        $this->view->setVars([
            'readposts'    => $readposts,
            'posts'        => $posts,
            'totalPosts'   => $totalPosts,
            'currentOrder' => null,
            'logged'       => $userId,
            'pager'        => $pager,
        ]);
    }

    /**
     * Add new category
     */
    public function createAction()
    {
        if ($this->session->get('identity-admin') !== 'Y') {
            throw new HttpException("Only administrators can create categories", 404);
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost('create-category')) {
                $this->response->redirect();

                return;
            }

            $name = $this->request->getPost('name', 'trim');

            $category = new Categories([
                'name'        => $name,
                'slug'        => $this->slug->generate($name),
                'description' => $this->request->getPost('description'),
                'no_bounty'   => $this->request->getPost('no_bounty', 'string', 'N'),
                'no_digest'   => $this->request->getPost('no_digest', 'string', 'N'),
            ]);

            if ($category->save()) {
                $this->response->redirect("category/{$category->id}/{$category->slug}");

                return;
            }

            $messages = $category->getMessages();
            if (count($messages)) {
                $errors = [];
                array_map(function ($message) use (&$errors) {
                    $errors[$message->getField()][] = $message->getMessage();
                }, $messages);

                $this->view->setVar('errors', $errors);
            }
        }

        $this->tag->setTitle('New Category');
    }

    /**
     * Reload categories
     */
    public function reloadCategoriesAction()
    {
        $this->view->setVar('categories', Categories::find(['order' => 'number_posts DESC, name']));
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->getCache()->delete('sidebar');
    }
}
