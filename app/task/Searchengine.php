<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Task;

use Elasticsearch\Client;
use Phosphorum\Model\Users;
use Phosphorum\Model\Posts;
use Phosphorum\Console\AbstractTask;
use Phosphorum\Services\SearchUserService;

/**
 * Phosphorum\Task\SearchEngine
 *
 * @package Phosphorum\Task
 */
class Searchengine extends AbstractTask
{
    /**
     * Elasticsearch client
     * @var Client
     */
    protected $client;

    /**
     * @Doc("Index all existing posts in the Forum to elastic search")
     */
    public function index()
    {
        $this->client = $this->getDI()->get('elastic');

        $this->outputMessage('Start');

        $this->outputMessage('Clear old indexes...');
        $this->deleteOldIndexes();

        $this->outputMessage('Reindex posts...');
        $this->reIndex();

        $this->outputMessage('Done');
    }

    /**
     * @Doc("Add users to search index")
     */
    public function addUsers()
    {
        $this->outputMessage('Start');
        $this->addUsersToSearchEngine(0, (new SearchUserService()));
        $this->outputMessage('Finish');
    }

    protected function deleteOldIndexes()
    {
        $index = $this->getDI()->get('config')->path('elasticsearch.index', 'phosphorum');

        if (!$this->client->indices()->exists(['index' => $index])) {
            // The index does not exist yet or got corrupted
            return;
        }

        $this->client->indices()->delete(['index' => $index]);
    }

    protected function reIndex()
    {
        $posts = Posts::find([
            'conditions' => 'deleted != :deleted:',
            'bind'       => [
                'deleted' => Posts::IS_DELETED
            ],
        ]);

        if (empty($posts)) {
            return;
        }

        $total = 0;
        foreach ($posts as $post) {
            $this->doIndex($post);
            $total++;
        }

        $this->output('Reindexed {total} posts', ['total' => $total]);
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
        $index = $this->getDI()->get('config')->path('elasticsearch.index', 'phosphorum');

        if ($karma > 0) {
            $params['body']  = [
                'id'       => $post->id,
                'title'    => $post->title,
                'category' => $post->categories_id,
                'content'  => $post->content,
                'karma'    => $karma
            ];
            $params['index'] = $index;
            $params['type']  = 'post';
            $params['id']    = 'post-' . $post->id;

            $this->client->index($params);
        }
    }

    /**
     * @param int $id
     * @param SearchUserService $searchUserService
     */
    private function addUsersToSearchEngine($id, $searchUserService)
    {
        $users = $this->getUser($id);
        if (empty($users)) {
            return;
        }

        foreach ($users as $user) {
            if (!$searchUserService->addUserToIndex($user)) {
                $this->outputError("User '{$user['id']}' hasn't been added to index");
            }
        }

        $id = end($users)['id'];
        $this->addUsersToSearchEngine($id, $searchUserService);
    }

    /**
     * @param int $id
     * @return array
     */
    private function getUser($id)
    {
        return Users::find([
            "banned = 'N' AND id > {$id}",
            'limit' => 300,
        ])->toArray();
    }
}
