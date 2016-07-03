<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Search;

use Elasticsearch\Client;
use Phalcon\Di\Injectable;
use Phosphorum\Models\Posts;

/**
 * Indexer
 *
 * This component uses ElasticSearch to search items in the forum
 */
class Indexer extends Injectable
{
    protected $logger;

    public function __construct()
    {
        $this->logger = $this->getDI()->get('logger', ['indexer.log']);
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
        try {
            $client = new Client();

            $searchParams['index'] = 'phosphorum';
            $searchParams['type']  = 'post';

            $searchParams['body']['fields'] = ['id', 'karma'];

            if (count($fields) == 1) {
                $searchParams['body']['query']['match'] = $fields;
            } else {
                $terms = [];
                foreach ($fields as $field => $value) {
                    $terms[] = ['term' => [$field => $value]];
                }
                $searchParams['body']['query']['bool']['must'] = $terms;
            }

            $searchParams['body']['from'] = 0;
            $searchParams['body']['size'] = $limit;

            $queryResponse = $client->search($searchParams);

            $results = [];
            if (is_array($queryResponse['hits'])) {
                $d = 0.5;
                foreach ($queryResponse['hits']['hits'] as $hit) {
                    if ($post = Posts::findFirstById($hit['fields']['id'][0])) {
                        if ($hit['fields']['karma'][0] > 0 && ($post->hasReplies() || $post->hasAcceptedAnswer())) {
                            $score = $hit['_score'] * 250 + $hit['fields']['karma'][0] + $d;
                            if (!$returnPosts) {
                                $results[$score] = [
                                    'slug'    => "discussion/{$post->id}/{$post->slug}",
                                    'title'   => $post->title,
                                    'created' => $post->getHumanCreatedAt()
                                ];
                            } else {
                                $results[$score] = $post;
                            }

                            $d += 0.05;
                        }
                    }
                }
            }

            krsort($results);

            return array_values($results);
        } catch (\Exception $e) {
            $this->logger->error("Indexer: {$e->getMessage()}. Line: {$e->getLine()}. File: {$e->getFile()}");
            return [];
        }
    }

    /**
     * Index a single document
     *
     * @param Client $client
     * @param Posts $post
     */
    protected function doIndex($client, $post)
    {
        $karma = $post->number_views + (($post->votes_up - $post->votes_down) * 10) + $post->number_replies;
        if ($karma > 0) {
            $params = [];
            $params['body']  = [
                'id'       => $post->id,
                'title'    => $post->title,
                'category' => $post->categories_id,
                'content'  => $post->content,
                'karma'    => $karma
            ];
            $params['index'] = 'phosphorum';
            $params['type']  = 'post';
            $params['id']    = 'post-' . $post->id;
            $ret = $client->index($params);
            var_dump($ret);
        }
    }

    public function searchCommon()
    {
        $client = new Client();

        $searchParams['index'] = 'phosphorum';
        $searchParams['type']  = 'post';

        $searchParams['body']['common']['body']['fields'] = ['id', 'karma'];
        $searchParams['body']['common']['body']['query'] = "nelly the elephant not as a cartoon";
        $searchParams['body']['common']['body']["cutoff_frequency"] = 0.001;

        $queryResponse = $client->search($searchParams);
    }

    /**
     * Puts a post in the search server
     *
     * @param Posts $post
     */
    public function index($post)
    {
        $client = new Client();
        $this->doIndex($client, $post);
    }

    /**
     * Indexes all posts in the forum in ES
     */
    public function indexAll()
    {
        $client = new Client();

        try {
            $deleteParams['index'] = 'phosphorum';
            $client->indices()->delete($deleteParams);
        } catch (\Exception $e) {
            // the index does not exist yet
        }

        foreach (Posts::find('deleted != 1') as $post) {
            $this->doIndex($client, $post);
        }
    }
}
