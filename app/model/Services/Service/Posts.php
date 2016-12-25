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

namespace Phosphorum\Model\Services\Service;

use Phalcon\Mvc\Model\Row;
use InvalidArgumentException;
use Phosphorum\Model\Posts as Entity;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phosphorum\Model\Services\AbstractService;

/**
 * Phosphorum\Model\Services\Service\Posts
 *
 * @package Phosphorum\Model\Services\Service
 */
class Posts extends AbstractService
{
    /**
     * Gets posts ordered by karma.
     *
     * Will return Simple result set with fields:
     * - `id`
     * - `slug`
     * - `karma`
     * - `modified`
     *
     * @param  int $karmaFactor
     * @return Simple
     */
    public function getPostsOrderedByKarma($karmaFactor = 4)
    {
        /** @var \Phalcon\Mvc\Model\Manager $modelsManager */
        $modelsManager = container('modelsManager');

        $karmaSql = 'p.number_views + ' .
                    "((COALESCE(p.votes_up, 0) - COALESCE(p.votes_down, 0)) * {$karmaFactor}) + " .
                    'p.number_replies';

        return $modelsManager->createBuilder()
            ->from(['p' => Entity::class])
            ->columns(['p.id', 'p.slug', 'p.modified_at AS modified', "{$karmaSql} AS karma"])
            ->where('p.deleted != :deleted:', ['deleted' => 1])
            ->orderBy(["p.karma DESC"])
            ->getQuery()
            ->execute();
    }

    /**
     * Gets the maximum of posts karma.
     *
     * @param  int $karmaFactor
     * @return int
     */
    public function getMostHighestPostsKarma($karmaFactor = 4)
    {
        $karmaSql = 'number_views + ' .
                    "((COALESCE(votes_up, 0) - COALESCE(votes_down, 0)) * {$karmaFactor}) + " .
                    'number_replies';

        return (int) Entity::maximum([
            'column'     => $karmaSql,
            'conditions' => 'deleted != 1'
        ]);
    }

    /**
     * Gets the Post URL.
     *
     * @param  Entity|Row $post
     * @return string
     *
     * @throes InvalidArgumentException
     */
    public function getPostUrl($post)
    {
        if (!$post instanceof Entity && !$post instanceof Row) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid application post type. Expected either "%s" or "%s". Got "%s".',
                    Entity::class,
                    Row::class,
                    gettype($post)
                )
            );
        }

        /** @var \Phalcon\Config $config */
        $config = container('config');

        $baseUrl = $config->get('site')->url;

        return strtr(':base:/discussion/:id:/:slug:', [
            ':base:' => rtrim($baseUrl, '/'),
            ':id:'   => $post->id,
            ':slug:' => $post->slug,
        ]);
    }
}
