<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
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
use Phosphorum\Models\Posts;

/**
 * Indexer
 *
 * This component uses ElasticSearch to search items in the forum
 */
class Indexer
{

    /**
     * Search documents in ElasticSearch by the specified criteria
     *
     * @param array $fields
     * @param int $limit
     * @param boolean $returnPosts
     */
    public function search(array $fields, $limit = 10, $returnPosts = false)
    {
        try {
            $client = new Client();

            $searchParams['index'] = 'phosphorum';
            $searchParams['type']  = 'post';

            $searchParams['body']['fields'] = array('id', 'karma');

            if (count($fields) == 1) {
                $searchParams['body']['query']['match'] = $fields;
            } else {
                $terms = array();
                foreach ($fields as $field => $value) {
                    $terms[] = array('term' => array($field => $value));
                }
                $searchParams['body']['query']['bool']['must'] = $terms;
            }

            $searchParams['body']['from'] = 0;
            $searchParams['body']['size'] = $limit;

            $queryResponse = $client->search($searchParams);

            $results = array();
            if (is_array($queryResponse['hits'])) {
                $d = 0.5;
                foreach ($queryResponse['hits']['hits'] as $hit) {
                    $post = Posts::findFirstById($hit['fields']['id'][0]);
                    if ($post) {
                        if ($hit['fields']['karma'][0] > 0 && ($post->number_replies > 0 || $post->accepted_answer == 'Y')) {
                            $score = $hit['_score'] * 250 + $hit['fields']['karma'][0] + $d;
                            if (!$returnPosts) {
                                $results[$score] = array(
                                    'slug'    => 'discussion/' . $post->id . '/' . $post->slug,
                                    'title'   => $post->title,
                                    'created' => $post->getHumanCreatedAt()
                                );
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
            return array();
        }
    }

    /**
     * Index a single document
     *
     * @param Client $client
     * @param Posts $post
     */
    protected function _doIndex($client, $post)
    {
        $karma = $post->number_views + (($post->votes_up - $post->votes_down) * 10) + $post->number_replies;
        if ($karma > 0) {
            $params = array();
            $params['body']  = array(
                'id'       => $post->id,
                'title'    => $post->title,
                'category' => $post->categories_id,
                'content'  => $post->content,
                'karma'    => $karma
            );
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

        $searchParams['body']['common']['body']['fields'] = array('id', 'karma');
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
        $this->_doIndex($client, $post);
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
            $this->_doIndex($client, $post);
        }
    }
}
