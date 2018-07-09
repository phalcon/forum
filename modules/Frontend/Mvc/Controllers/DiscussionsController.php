<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Frontend\Mvc\Controllers;

use Phosphorum\Core\Paginator\PaginatorManager;
use Phosphorum\Domain\Factories\CategoryFactory;
use Phosphorum\Domain\Factories\PostFactory;
use Phosphorum\Domain\Factories\PostTrackingFactory;
use Phosphorum\Domain\Services\CategoryService;
use Phosphorum\Domain\Services\PostService;
use Phosphorum\Domain\Services\PostTrackingService;

/**
 * Phosphorum\Frontend\Mvc\Controllers\DiscussionsController
 *
 * @package Phosphorum\Frontend\Mvc\Controllers
 */
class DiscussionsController extends Controller
{
    /** @var PostTrackingService */
    private $postTrackingService;

    /** @var PostService */
    private $postService;

    /** @var CategoryService */
    private $categoryService;

    /** @var PaginatorManager */
    private $paginatorManager;

    /** @var null|int */
    private $userId = null;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->postTrackingService = $this->getDI()
            ->get(PostTrackingFactory::class)
            ->createService();

        $this->postService = $this->getDI()
            ->get(PostFactory::class)
            ->createService();

        $this->categoryService = $this->getDI()
            ->get(CategoryFactory::class)
            ->createService();

        $this->paginatorManager = $this->getDI()->get(PaginatorManager::class);

        if ($this->session->has('identity')) {
            $this->userId = (int) $this->session->get('identity');
        }
    }

    public function hotAction(?string $offset = null): void
    {
        $this->tag->setTitle('Hot Discussions');

        $offset = $this->getPostsOffset($offset);

        $this->view->setVars([
            'canonical' => $this->getCanonicalUri($offset),
            'user_id' => $this->userId,
            'read_posts' => $this->postTrackingService->getReadPostsIds($this->userId),
            'posts' => $this->postService->getPopularPosts(),
            'categories' => $this->categoryService->getOrderedList(),
            'pager' => $this->createPager($offset),
        ]);
    }

    public function myAction(?string $offset = null): void
    {
        // todo: Prevent to see by unauthorized users
        $this->tag->setTitle('My Discussions');

        $offset = $this->getPostsOffset($offset);

        $this->view->setVars([
            'canonical' => $this->getCanonicalUri($offset),
            'user_id' => $this->userId,
            'read_posts' => $this->postTrackingService->getReadPostsIds($this->userId),
            'posts' => $this->postService->getPopularPosts(), // todo
            'categories' => $this->categoryService->getOrderedList(),
            'pager' => $this->createPager($offset),
        ]);
    }

    public function unansweredAction(?string $offset = null): void
    {
        $this->tag->setTitle('Unanswered Discussions');

        $offset = $this->getPostsOffset($offset);

        $this->view->setVars([
            'canonical' => $this->getCanonicalUri($offset),
            'user_id' => $this->userId,
            'read_posts' => $this->postTrackingService->getReadPostsIds($this->userId),
            'posts' => $this->postService->getPopularPosts(), // todo
            'categories' => $this->categoryService->getOrderedList(),
            'pager' => $this->createPager($offset),
        ]);
    }

    public function answersAction(?string $offset = null): void
    {
        // todo: Prevent to see by unauthorized users
        $this->tag->setTitle('My Answers');

        $offset = $this->getPostsOffset($offset);

        $this->view->setVars([
            'canonical' => $this->getCanonicalUri($offset),
            'user_id' => $this->userId,
            'read_posts' => $this->postTrackingService->getReadPostsIds($this->userId),
            'posts' => $this->postService->getPopularPosts(), // todo
            'categories' => $this->categoryService->getOrderedList(),
            'pager' => $this->createPager($offset),
        ]);
    }

    public function newAction(?string $offset = null): void
    {
        $this->tag->setTitle('All Discussions');

        $offset = $this->getPostsOffset($offset);

        $this->view->setVars([
            'canonical' => $this->getCanonicalUri($offset),
            'user_id' => $this->userId,
            'read_posts' => $this->postTrackingService->getReadPostsIds($this->userId),
            'posts' => $this->postService->getPopularPosts(), // todo
            'categories' => $this->categoryService->getOrderedList(),
            'pager' => $this->createPager($offset),
        ]);
    }

    public function viewAction(string $id, ?string $slug = null): void
    {
        echo json_encode([
            __METHOD__,
            '$id' => $id,
            '$slug' => $slug,
            $this->dispatcher->getParams()
        ]);
    }

    private function getCanonicalUri(?int $offset = null): string
    {
        $routeName = $offset ? 'discussions-order-offset' : 'discussions-order';
        $actionName = $this->dispatcher->getActionName();

        return $this->url->get(['for' => $routeName, 'action' => $actionName, 'offset' => $offset]);
    }

    private function getCurrentPage(?int $offset = null): int
    {
        if ($offset > 0) {
            $postsPerPage = $this->paginatorManager->getPostsPerPageLimit();
            $actualOffset = $offset - ceil($offset % $this->paginatorManager->getPostsPerPageLimit());

            return (int) ($actualOffset / $postsPerPage) + 1;
        }

        $currentPage = abs($this->request->getQuery('page', 'int'));

        if ($currentPage == 0) {
            $currentPage = 1;
        }

        return $currentPage;
    }

    private function getPostsOffset($offset = null): ?int
    {
        return $offset = $offset !== null ? (int) $offset : $offset;;
    }

    private function createPager(?int $offset = null)
    {
        return $this->paginatorManager->createPager(
            $this->postService->getPaginatorQueryBuilder(),
            sprintf('%s?page={%%page_number}', $this->getCanonicalUri()),
            $this->getCurrentPage($offset)
        );
    }
}
