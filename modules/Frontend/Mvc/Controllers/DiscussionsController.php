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

use Phosphorum\Domain\Services\PostTrackingService;
use Phosphorum\Domain\Factories\PostTrackingFactory;

/**
 * Phosphorum\Frontend\Mvc\Controllers\DiscussionsController
 *
 * @package Phosphorum\Frontend\Mvc\Controllers
 */
class DiscussionsController extends Controller
{
    /** @var PostTrackingService */
    protected $postService;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->postService = $this->getDI()
            ->get(PostTrackingFactory::class)
            ->createService();
    }

    /**
     * Shows latest posts using an order clause
     *
     * @param  string $order
     * @param  int    $offset
     *
     * @return void
     */
    public function indexAction(string $order = null, int $offset = 0): void
    {
        $this->appendTitle($order);

        $userId = $this->session->get('identity');

        $this->view->setVars([
            'canonical' => '',
            'readposts' => $this->postService->getReadPostsIds($userId),
        ]);
    }

    private function appendTitle(string $order = null): void
    {
        switch ($order) {
            case 'hot':
                $this->tag->setTitle('Hot Discussions');
                break;
            case 'my':
                $this->tag->setTitle('My Discussions');
                break;
            case 'unanswered':
                $this->tag->setTitle('Unanswered Discussions');
                break;
            case 'answers':
                $this->tag->setTitle('My Answers');
                break;
            default:
                $this->tag->setTitle('Discussions');
        }
    }
}
