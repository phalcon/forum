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

use Phalcon\Mvc\View;
use Phosphorum\Model\Categories;
use Phosphorum\Model\TopicTracking;
use Phosphorum\Mvc\Controller\TokenTrait;

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

        $totalBuilder->where('p.categories_id = ?0 AND p.deleted = 0');

        $posts = $itemBuilder
            ->where('p.categories_id = ?0 AND p.deleted = 0')
            ->orderBy('p.created_at DESC')
            ->offset((int) $offset)
            ->getQuery()
            ->execute([$categoryId]);

        if (!count($posts)) {
            $this->flashSession->notice('There are no posts in category: ' . $category->name);
            $this->response->redirect();
            return;
        }

        $totalPosts = $totalBuilder
            ->getQuery()
            ->setUniqueRow(true)
            ->execute([$categoryId]);

        $this->view->setVars([
            'readposts'    => $readposts,
            'posts'        => $posts,
            'totalPosts'   => $totalPosts,
            'currentOrder' => null,
            'offset'       => (int) $offset,
            'paginatorUri' => "category/{$category->id}/{$category->slug}",
            'logged'       => $userId
        ]);
    }

    /**
     * Add new category
     */
    public function createAction()
    {
        if ($this->session->get('identity-admin') !== 'Y') {           
            $this->response->redirect('/404');
            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost('create-category')) {
                $this->response->redirect();
                return;
            }

            $name = $this->request->getPost('name', 'trim');

            $category = new Categories([
                'name' => $name,
                'slug' => $this->slug->generate($name),
                'description' => $this->request->getPost('description'),
                'no_bounty' => $this->request->getPost('no_bounty', 'string', 'N'),
                'no_digest' => $this->request->getPost('no_digest', 'string', 'N'),
            ]);

            if ($category->save()) {
                $this->response->redirect("discussion/{$category->id}/{$category->slug}");
                return;
            }
            
            $this->flashSession->error(join('<br>', $category->getMessages()));
        }

        $this->tag->setTitle('Creation of Category');
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
