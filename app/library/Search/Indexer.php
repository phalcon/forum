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

namespace Phosphorum\Search;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phosphorum\Model\Posts;
use Phalcon\Logger\AdapterInterface;
use Elasticsearch\Common\Exceptions\Missing404Exception;

/**
 * Indexer
 *
 * This component uses ElasticSearch to search items in the forum
 */
class Indexer extends Injectable
{
    /**
     * Application logger
     * @var AdapterInterface
     */
    protected $logger;

    /**
     * Elasticsearch client
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * Indexer config
     * @var Config
     */
    protected $config;

    public function __construct()
    {
        $this->logger = $this->getDI()->get('logger', ['indexer']);

        $config = $this->getDI()->getShared('config');
        $this->config = $config->get('elasticsearch', new Config);

        $this->client = $this->getDI()->getShared('elastic');
    }

    /**
     * Search documents in ElasticSearch by the specified criteria
     *
     * @param array $fields
     * @param int   $limit
     * @param bool  $returnPosts
     * @return array
     */
    public function search(array $fields, $limit = 10, $returnPosts = false)
    {
        $results = [];

        $searchParams = [
            'index' => $this->config->get('index', 'phosphorum'),
            'type'  => 'post',
            'body'  => [
                'fields' => ['id', 'karma'],
                'query'  => [],
                'from'   => 0,
                'size'   => intval($limit),
            ]
        ];

        if (count($fields) == 1) {
            $searchParams['body']['query']['match'] = $fields;
        } else {
            $terms = [];
            foreach ($fields as $field => $value) {
                $terms[] = ['term' => [$field => $value]];
            }

            $searchParams['body']['query']['bool'] = [
                'must' => $terms
            ];
        }

        try {
            $queryResponse = $this->client->search($searchParams);
            $queryResponse = $this->parseElasticResponse($queryResponse);

            $d = 0.5;
            foreach ($queryResponse as $hit) {
                if (!isset($hit['fields']['id'][0])) {
                    continue;
                }

                $id = $hit['fields']['id'][0];
                $post = Posts::findFirstById($id);

                if (!$post || $post->deleted == Posts::IS_DELETED) {
                    continue;
                }

                if ($hit['fields']['karma'][0] > 0 && ($post->hasReplies() || $post->hasAcceptedAnswer())) {
                    $score = $hit['_score'] * 250 + $hit['fields']['karma'][0] + $d;
                    if (!$returnPosts) {
                        $results[$score] = $this->createPostArray($post);
                    } else {
                        $results[$score] = $post;
                    }

                    $d += 0.05;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("Indexer: {$e->getMessage()}. {$e->getFile()}:{$e->getLine()}");
        }

        krsort($results);

        return array_values($results);
    }

    /**
     * Puts a post in the search server
     *
     * @param Posts $post
     */
    public function index(Posts $post)
    {
        $this->doIndex($post);
    }

    /**
     * Indexes all posts in the forum in ES
     */
    public function indexAll()
    {
        $deleteParams = [
            'index' => $this->config->get('index', 'phosphorum'),
        ];

        try {
            $this->client->indices()->delete($deleteParams);
        } catch (Missing404Exception $e) {
            $this->logger->info('The index does not exist yet. Skip deleting...');
        } catch (\Exception $e) {
            $this->logger->error("Indexer: {$e->getMessage()}. {$e->getFile()}:{$e->getLine()}");
        }

        foreach (Posts::find('deleted != ' . Posts::IS_DELETED) as $post) {
            $this->doIndex($post);
        }
    }

    /**
     * Index a single document
     *
     * @param Posts $post
     */
    protected function doIndex(Posts $post)
    {
        $params = [];
        $karma  = $post->number_views + (($post->votes_up - $post->votes_down) * 10) + $post->number_replies;

        if ($karma > 0) {
            $params['body']  = [
                'id'       => $post->id,
                'title'    => $post->title,
                'category' => $post->categories_id,
                'content'  => $post->content,
                'karma'    => $karma
            ];
            $params['index'] = $this->config->get('index', 'phosphorum');
            $params['type']  = 'post';
            $params['id']    = 'post-' . $post->id;

            $this->client->index($params);
        }
    }

    protected function createPostArray(Posts $post)
    {
        return [
            'slug'    => "discussion/{$post->id}/{$post->slug}",
            'title'   => $post->title,
            'created' => $post->getHumanCreatedAt(),
        ];
    }

    /**
     * Parse Elasticsearch response
     *
     * @param mixed $response
     * @return array
     * @throws \Exception
     */
    protected function parseElasticResponse($response)
    {
        if (!isset($response['hits']) || !isset($response['hits']['hits']) || !is_array($response['hits']['hits'])) {
            throw new \Exception('The Elasticsearch client does not return expected response.');
        }

        return $response['hits']['hits'];
    }
}
