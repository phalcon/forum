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

namespace Phosphorum\Core\Paginator;

use Phalcon\Config;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Paginator\Adapter\QueryBuilder;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Pager\Layout\Bootstrap;
use Phalcon\Paginator\Pager\Range\Sliding;
use Phalcon\Platform\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Paginator\PaginatorManager
 *
 * @package Phosphorum\Core\Paginator
 */
final class PaginatorManager implements InjectionAwareInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /** @var string */
    protected $layoutClass;

    /** @var string */
    protected $rangeClass;

    /** @var int */
    protected $rangeLength;

    /** @var int */
    protected $postLimit;

    /**
     * PaginatorManager constructor.
     *
     * @param Config           $config
     * @param null|DiInterface $container
     */
    public function __construct(Config $config, ?DiInterface $container = null)
    {
        $this->__DiInject($container);

        $this->layoutClass = $config->get('layoutClass', Bootstrap::class);
        $this->rangeClass = $config->get('rangeClass', Sliding::class);
        $this->rangeLength = $config->get('rangeLength', 10);
        $this->postLimit = $config->get('postLimit', 20);
    }

    /**
     * Factory method to create a Pager instance.
     *
     * @param  BuilderInterface $queryBuilder
     * @param  string           $urlMask
     * @param  int              $currentPage
     * @param  int|null         $limit
     *
     * @return Pager
     */
    public function createPager(
        BuilderInterface $queryBuilder,
        string $urlMask,
        int $currentPage,
        ?int $limit = null
    ): Pager {
        $paginator = new QueryBuilder([
            'builder' => $queryBuilder,
            'limit' => $limit ?: $this->postLimit,
            'page' => $currentPage,
        ]);

        return new Pager($paginator, [
            'layoutClass' => $this->layoutClass,
            'rangeClass' => $this->rangeClass,
            'rangeLength' => $this->rangeLength,
            'urlMask' => $urlMask,
        ]);
    }

    /**
     * Gets limit posts per page.
     *
     * @return int
     */
    public function getPostsPerPageLimit(): int
    {
        return $this->postLimit;
    }
}
