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
     * Indexer constructor.
     */
    public function __construct()
    {
        $this->logger = $this->getDI()->get('logger', ['indexer']);
        $this->client = container('elastic');
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
            'index' => container('config')->path('elasticsearch.index', 'phosphorum'),
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
                // TODO: Log each $value into database in order to analyze popular queries
                $terms[] = ['term' => [$field => $value]];
            }

            $searchParams['body']['query']['bool'] = [
                'must' => $terms
            ];
        }

        try {
            $this->logger->error(json_encode($searchParams, JSON_PRETTY_PRINT));
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

            krsort($results);

            return array_values($results);
        } catch (Missing404Exception $e) {
            $this->logger->info('The index does not exist yet or get corrupted');
            return [];
        } catch (\Exception $e) {
            $this->logger->error(
                'Indexer: ({exception}) {message} in {file}:{line}',
                [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]
            );
            return [];
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
