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

namespace Phosphorum\Frontend\Mvc\QueryBuilders;

use Phosphorum\Core\Traits\InjectionAwareTrait;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Query\BuilderInterface;

/**
 * Phosphorum\Frontend\Mvc\QueryBuilders\PostsBuilder
 *
 * @package Phosphorum\Frontend\Mvc\QueryBuilders
 */
final class PostsBuilder implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Prepares the item builder to be executed in each list of posts.
     *
     * The returned builder will be used as base in the search, tagged list and index lists.
     *
     * @param  bool $joinReply
     * @param  int  $postsPerPage
     *
     * @return BuilderInterface|Builder
     */
    public function createItemBuilder(bool $joinReply = false, int $postsPerPage = 40): BuilderInterface
    {
        $itemBuilder = $this->createBuilder($joinReply);

        return $itemBuilder
            ->columns(['p.*'])
            ->limit($postsPerPage);
    }

    /**
     * Prepares the total builder to be executed in each list of posts.
     *
     * The returned builder will be used as base in the search, tagged list and index lists.
     *
     * @param  bool $joinReply
     *
     * @return BuilderInterface|Builder
     */
    public function createTotalBuilder(bool $joinReply = false): BuilderInterface
    {
        $totalBuilder = $this->createBuilder($joinReply);

        return $totalBuilder
            ->columns('COUNT(*) AS count');
    }

    /**
     * Create internal query builder.
     *
     * @see PostsBuilder::createItemBuilder
     * @see PostsBuilder::createTotalBuilder
     *
     * @param  bool $joinReply
     *
     * @return BuilderInterface|Builder
     */
    protected function createBuilder(bool $joinReply = false): BuilderInterface
    {
        /** @var Manager $modelsManager */
        $modelsManager = $this->getDI()->getShared('modelsManager');

        $itemBuilder = $modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Model\Posts'])
            ->orderBy('p.sticked DESC, p.created_at DESC');

        if ($joinReply == true) {
            $itemBuilder
                ->groupBy('p.id')
                ->join('Phosphorum\Model\PostsReplies', 'r.posts_id = p.id', 'r');
        }

        return $itemBuilder;
    }
}
