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

namespace Phosphorum\Controllers;

use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsViews;
use Phosphorum\Models\PostsReplies;
use Phosphorum\Models\PostsBounties;
use Phosphorum\Models\PostsHistory;
use Phosphorum\Models\PostsVotes;
use Phosphorum\Models\Categories;
use Phosphorum\Models\Activities;
use Phosphorum\Models\IrcLog;
use Phosphorum\Models\Users;
use Phosphorum\Models\Karma;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

/**
 * Class DiscussionsController
 *
 * @package Phosphorum\Controllers
 */
class DiscussionsController extends Controller
{

    const POSTS_IN_PAGE = 40;

    /**
     * This initializes the timezone in each request
     */
    public function initialize()
    {
        $timezone = $this->session->get('identity-timezone');
        if ($timezone) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * This method prepares the queries to be executed in each list of posts
     * The returned builders are used as base in the search, tagged list and index lists
     */
    protected function prepareQueries($joinReply = false)
    {

        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        $itemBuilder = $this
            ->modelsManager
            ->createBuilder()
            ->from(array('p' => 'Phosphorum\Models\Posts'))
            ->orderBy('p.sticked DESC, p.created_at DESC');

        if ($joinReply) {
            $itemBuilder
                ->groupBy("p.id")
                ->join('Phosphorum\Models\PostsReplies', "r.posts_id = p.id", 'r');
        }

        $totalBuilder = clone $itemBuilder;

        $itemBuilder
            ->columns(array('p.*'))
            ->limit(self::POSTS_IN_PAGE);

        $totalBuilder
            ->columns('COUNT(*) AS count');

        return array($itemBuilder, $totalBuilder);
    }

    /**
     * Shows latest posts using an order clause
     */
    public function indexAction($order = null, $offset = 0)
    {

        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder */

        if ($order == "answers") {
            list($itemBuilder, $totalBuilder) = $this->prepareQueries(true);
        } else {
            list($itemBuilder, $totalBuilder) = $this->prepareQueries();
        }

        /**
         * Create the conditions according to the parameter order
         */

        $userId = $this->session->get('identity');

        $params = null;
        switch ($order) {

            case 'hot':
                $this->tag->setTitle('Hot Discussions');
                $itemBuilder->orderBy('p.modified_at DESC');
                break;

            case 'my':
                $this->tag->setTitle('My Discussions');
                if ($userId) {
                    $params       = array($userId);
                    $myConditions = 'p.users_id = ?0';
                    $itemBuilder->where($myConditions);
                    $totalBuilder->where($myConditions);
                }
                break;

            case 'unanswered':
                $this->tag->setTitle('Unanswered Discussions');
                $unansweredConditions = 'p.number_replies = 0 AND p.accepted_answer <> "Y"';
                $itemBuilder->where($unansweredConditions);
                $totalBuilder->where($unansweredConditions);
                break;

            case 'answers':
                $this->tag->setTitle('My Answers');
                if ($userId) {
                    $params            = array($userId);
                    $answersConditions = 'r.users_id = ?0';
                    $itemBuilder->where($answersConditions);
                    $totalBuilder->where($answersConditions);
                }
                break;

            default:
                $this->tag->setTitle('Discussions');
        }

        $notDeleteConditions = 'p.deleted = 0';
        $itemBuilder->andWhere($notDeleteConditions);
        $totalBuilder->andWhere($notDeleteConditions);

        if ($offset > 0) {
            $itemBuilder->offset((int)$offset);
        }

        $this->view->posts      = $itemBuilder->getQuery()->execute($params);
        $this->view->totalPosts = $totalBuilder->getQuery()->setUniqueRow(true)->execute($params);

        if (!$order) {
            $order = 'new';
        }

        $this->view->currentOrder = $order;
        $this->view->offset       = $offset;
        $this->view->paginatorUri = 'discussions/' . $order;
        $this->view->canonical    = '';
    }

    /**
     * Shows latest posts by category
     */
    public function categoryAction($categoryId, $slug, $offset = 0)
    {
        $this->tag->setTitle('Discussions');

        $category = Categories::findFirstById($categoryId);
        if (!$category) {
            $this->flashSession->notice('The category doesn\'t exist');
            return $this->response->redirect();
        }

        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder */
        list($itemBuilder, $totalBuilder) = $this->prepareQueries();

        $totalBuilder->where('p.categories_id = ?0 AND p.deleted = 0');

        $posts = $itemBuilder
            ->where('p.categories_id = ?0 AND p.deleted = 0')
            ->orderBy('p.created_at DESC')
            ->offset((int)$offset)
            ->getQuery()
            ->execute(array($categoryId));

        if (!count($posts)) {
            $this->flashSession->notice('There are no posts in category: ' . $category->name);
            return $this->response->redirect();
        }

        $totalPosts = $totalBuilder->getQuery()->setUniqueRow(true)->execute(array($categoryId));

        $this->view->posts        = $posts;
        $this->view->totalPosts   = $totalPosts;
        $this->view->currentOrder = null;
        $this->view->offset       = (int)$offset;
        $this->view->paginatorUri = 'category/' . $category->id . '/' . $category->slug;
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function createAction()
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }


        $this->tag->setTitle('Start a Discussion');

        if ($this->request->isPost()) {

            $title = $this->request->getPost('title', 'trim');

            /** @var Users $user */
            $user = Users::findFirstById($usersId);
            $user->increaseKarma(Karma::ADD_NEW_POST);
            $user->save();

            $post                = new Posts();
            $post->users_id      = $usersId;
            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->tag->friendlyTitle($title);
            $post->content       = $this->request->getPost('content');

            if ($post->save()) {
                return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
            }

            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }
        }


        $parameters = array(
            'order' => 'name'
        );

        $this->view->categories = Categories::find($parameters);
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function editAction($id)
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        /**
         * Find the post using get
         */
        $parameters = array(
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            "bind" => array($id, $usersId, $this->session->get('identity-moderator'))
        );
        $post = Posts::findFirst($parameters);

        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($this->request->isPost()) {

            $title   = $this->request->getPost('title', 'trim');
            $content = $this->request->getPost('content');

            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->tag->friendlyTitle($title);
            $post->content       = $content;
            $post->edited_at     = time();

            $usersId = $this->session->get('identity');
            if ($post->users_id != $usersId) {

                /** @var Users $user */
                $user = Users::findFirstById($usersId);
                if ($user) {
                    $user->increaseKarma(Karma::MODERATE_POST);
                    $user->save();
                }
            }

            if ($post->save()) {
                return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
            }

            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

        } else {

            $this->tag->displayTo('id', $post->id);
            $this->tag->displayTo('title', $post->title);
            $this->tag->displayTo('content', $post->content);
            $this->tag->displayTo('categoryId', $post->categories_id);
        }

        $this->tag->setTitle('Edit Discussion: ' . $this->escaper->escapeHtml($post->title));

        $parametersCategory = array(
            'order' => 'name'
        );

        $this->view->categories = Categories::find($parametersCategory);

        $this->view->post = $post;
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function deleteAction($id)
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        /**
         * Find the post using get
         */
        $parameters = array(
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            "bind" => array($id, $usersId, $this->session->get('identity-moderator'))
        );
        $post = Posts::findFirst($parameters);

        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($post->sticked == 'Y') {
            $this->flashSession->error('The discussion cannot be deleted because it\'s sticked');
            return $this->response->redirect();
        }

        $post->deleted = 1;
        if ($post->save()) {

            $usersId = $this->session->get('identity');
            if ($post->users_id != $usersId) {

                /** @var Users $user */
                $user = Users::findFirstById($usersId);
                if ($user) {
                    if ($user->moderator == 'Y') {
                        $user->increaseKarma(Karma::MODERATE_DELETE_POST);
                        $user->save();
                    }
                }

                $user = $post->user;
                $user->decreaseKarma(Karma::DELETE_POST);
                $user->save();
            }

            $this->flashSession->success('Discussion was successfully deleted');
            return $this->response->redirect();
        }
    }

    /**
     * Displays a post and its comments
     *
     * @param $id
     * @param $slug
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function viewAction($id, $slug)
    {
        $id = (int)$id;

        $usersId = $this->session->get('identity');

        if (!$this->request->isPost()) {

            /**
             * Find the post using get
             */
            $post = Posts::findFirstById($id);
            if (!$post) {
                $this->flashSession->error('The discussion does not exist');
                return $this->response->redirect();
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                return $this->response->redirect();
            }

            $ipAddress = $this->request->getClientAddress();

            $parameters = array(
                'posts_id = ?0 AND ipaddress = ?1',
                'bind' => array($id, $ipAddress)
            );
            $viewed = PostsViews::count($parameters);

            /**
             * A view is stored by ipaddress
             */
            if (!$viewed) {

                /**
                 * Increase the number of views in the post
                 */
                $post->number_views++;
                if ($post->users_id != $usersId) {

                    $post->user->increaseKarma(Karma::VISIT_ON_MY_POST);

                    if ($usersId > 0) {

                        /** @var Users $user */
                        $user = Users::findFirstById($usersId);
                        if ($user) {
                            if ($user->moderator == 'Y') {
                                $user->increaseKarma(Karma::MODERATE_VISIT_POST);
                            } else {
                                $user->increaseKarma(Karma::VISIT_POST);
                            }
                            $user->save();
                        }
                    }
                }

                $postView            = new PostsViews();
                $postView->post      = $post;
                $postView->ipaddress = $ipAddress;
                if (!$postView->save()) {
                    foreach ($postView->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
            }

            if (!$usersId) {

                /**
                 * Enable cache
                 */
                $this->view->cache(array('key' => 'post-' . $id));

                /**
                 * Check for a cache
                 */
                if ($this->viewCache->exists('post-' . $id)) {
                    return;
                }
            }

            /**
             * Generate cannonical meta
             */
            $this->view->canonical = 'discussion/' . $post->id . '/' . $post->slug;

        } else {

            if (!$usersId) {
                $this->flashSession->error('You must be logged in first to add a comment');
                return $this->response->redirect();
            }

            /**
             * Find the post using POST
             */
            $post = Posts::findFirstById($this->request->getPost('id'));
            if (!$post) {
                $this->flashSession->error('The discussion does not exist');
                return $this->response->redirect();
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                return $this->response->redirect();
            }

            $content = $this->request->getPost('content', 'trim');
            if ($content) {

                /**
                 * Check if the question can have a bounty before add the reply
                 */
                $canHaveBounty = $post->canHaveBounty();

                $user = Users::findFirstById($usersId);
                if (!$user) {
                    $this->flashSession->error('You must be logged in first to add a comment');
                    return $this->response->redirect();
                }

                /**
                 * Only update the number of replies if the user that commented isn't the same that posted
                 */
                if ($post->users_id != $usersId) {

                    $post->number_replies++;
                    $post->modified_at = time();
                    $post->user->increaseKarma(Karma::SOMEONE_REPLIED_TO_MY_POST);

                    $user->increaseKarma(Karma::REPLY_ON_SOMEONE_ELSE_POST);
                    $user->save();
                }

                $postReply                 = new PostsReplies();
                $postReply->post           = $post;
                $postReply->in_reply_to_id = $this->request->getPost('reply-id', 'int');
                $postReply->users_id       = $usersId;
                $postReply->content        = $content;

                if ($postReply->save()) {

                    if ($post->users_id != $usersId && $canHaveBounty) {
                        $bounty                       = $post->getBounty();
                        $postBounty                   = new PostsBounties();
                        $postBounty->posts_id         = $post->id;
                        $postBounty->users_id         = $usersId;
                        $postBounty->posts_replies_id = $postReply->id;
                        $postBounty->points           = $bounty['value'];
                        if (!$postBounty->save()) {
                            foreach ($postBounty->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        }
                    }

                    $href = 'discussion/' . $post->id . '/' . $post->slug . '#C' . $postReply->id;
                    return $this->response->redirect($href);
                }

                foreach ($postReply->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }
        }

        /**
         * Set the post name as title - escaping it first
         */
        $this->tag->setTitle($this->escaper->escapeHtml($post->title) . ' - Discussion');

        $this->view->post = $post;
    }

    /**
     * Shows the latest modification made to a post
     */
    public function historyAction($id = 0)
    {

        $this->view->disable();

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $a = explode("\n", $post->content);

        $first         = true;
        $parameters    = array('posts_id = ?0', 'bind' => array($post->id), 'order' => 'created_at DESC');

        /** @var PostsHistory[] $postHistories */
        $postHistories = PostsHistory::find($parameters);

        if (count($postHistories) > 1) {
            foreach ($postHistories as $postHistory) {
                if ($first) {
                    $first = false;
                    continue;
                }
                break;
            }
        } else {
            $postHistory = $postHistories->getFirst();
        }

        if (is_object($postHistory)) {
            $b = explode("\n", $postHistory->content);

            $diff     = new \Diff($b, $a, array());
            $renderer = new \Diff_Renderer_Html_SideBySide();

            echo $diff->Render($renderer);
        } else {
            $this->flash->notice('No history available to show');
        }
    }

    /**
     * Votes a post up
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentlogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }

        if (PostsVotes::count(array('posts_id = ?0 AND users_id = ?1', 'bind' => array($post->id, $user->id)))) {
            $contentAlreadyVote = array(
                'status'  => 'error',
                'message' => 'You have already voted this post'
            );
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        if (!$postVote->save()) {
            foreach ($postVote->getMessages() as $message) {
                $contentError = array(
                    'status'  => 'error',
                    'message' => $message->getMessage()
                );
                return $response->setJsonContent($contentError);
            }
        }

        $post->votes_up++;
        if ($post->users_id != $user->id) {
            $post->user->increaseKarma(Karma::SOMEONE_DID_VOTE_MY_POST);
            $user->increaseKarma(Karma::VOTE_ON_SOMEONE_ELSE_POST);
        }

        if ($post->save()) {
            $user->votes--;
            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentErrorSave = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Votes a post down
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentlogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }

        if (PostsVotes::count(array('posts_id = ?0 AND users_id = ?1', 'bind' => array($post->id, $user->id)))) {
            $contentAlreadyVote = array(
                'status'  => 'error',
                'message' => 'You have already voted this post'
            );
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        $postVote->save();

        $post->votes_down++;
        if ($post->users_id != $user->id) {
            $post->user->decreaseKarma(Karma::SOMEONE_DID_VOTE_MY_POST);
            $user->increaseKarma(Karma::VOTE_ON_SOMEONE_ELSE_POST);
        }

        if ($post->save()) {
            $user->votes--;
            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentErrorSave = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Shows the latest activity on the IRC
     */
    public function ircAction()
    {

        $parameters = array(
            'order' => 'datelog DESC',
            'limit' => 250
        );
        $irclog = IrcLog::find($parameters);

        $activities = array();
        foreach ($irclog as $log) {
            $who          = explode('@', $log->who);
            $parts        = explode('!', $who[0]);
            $log->who     = substr($parts[0], 1);
            $activities[] = $log;
        }

        $this->view->activities = array_reverse($activities);

        $this->tag->setTitle('Recent Activity on the IRC');
    }

    /**
     * Shows the latest activity on the forum
     */
    public function activityAction($offset = 0)
    {

        $this->view->total = Activities::count();

        $parameters             = array(
            'order' => 'created_at DESC',
            'limit' => array('number' => self::POSTS_IN_PAGE, 'offset' => 0)
        );
        $this->view->activities = Activities::find($parameters);

        $this->tag->setTitle('Recent Activity on the Forum');
    }

    /**
     * Perform the search of posts only searching in the title
     */
    public function searchAction()
    {

        $this->tag->setTitle('Search Results');

        list($itemBuilder, $totalBuilder) = $this->prepareQueries();

        $q = $this->request->getQuery('q');

        $queryTerms = '%' . preg_replace('/[ \t]+/', '%', $q) . '%';

        $totalBuilder->where('p.title LIKE ?0');

        $itemBuilder->where('p.title LIKE ?0')->orderBy('p.created_at DESC');

        $posts = $itemBuilder->getQuery()->execute(array($queryTerms));

        if (!count($posts)) {
            $this->flashSession->notice('There are no search results');
            return $this->response->redirect();
        }

        $totalPosts = $totalBuilder->getQuery()->setUniqueRow(true)->execute(array($queryTerms));

        $this->view->posts        = $posts;
        $this->view->totalPosts   = $totalPosts;
        $this->view->currentOrder = null;
        $this->view->offset       = 0;
        $this->view->paginatorUri = 'search';
    }

    /**
     *
     */
    public function reloadCategoriesAction()
    {
        $parameters             = array(
            'order' => 'number_posts DESC, name'
        );
        $this->view->categories = Categories::find($parameters);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->getCache()->delete('sidebar');
    }

    /**
     * Shows the user profile
     */
    public function userAction($id, $username)
    {
        if ($id) {
            $user = Users::findFirstById($id);
        } else {
            $user = Users::findFirstByLogin($username);
            if (!$user) {
                $user = Users::findFirstByName($username);
            }
        }

        if (!$user) {
            $this->flashSession->error('The user does not exist');
            return $this->response->redirect();
        }

        $this->view->user = $user;

        $parametersNumberPosts              = array(
            'users_id = ?0 AND deleted = 0',
            'bind' => array($user->id)
        );
        $this->view->numberPosts = Posts::count($parametersNumberPosts);

        $parametersNumberReplies   = array(
            'users_id = ?0',
            'bind' => array($user->id)
        );
        $this->view->numberReplies = PostsReplies::count($parametersNumberReplies);

        $parametersActivities   = array(
            'users_id = ?0',
            'bind'  => array($user->id),
            'order' => 'created_at DESC',
            'limit' => 15
        );
        $this->view->activities = Activities::find($parametersActivities);

        $users   = Users::find(array('columns' => 'id', 'conditions' => 'karma != 0', 'order' => 'karma DESC'));
        $ranking = count($users);
        foreach ($users as $position => $everyUser) {
            if ($everyUser->id == $user->id) {
                $ranking = $position + 1;
                break;
            }
        }

        $this->view->ranking       = $ranking;
        $this->view->total_ranking = count($users);

        $this->tag->setTitle('Profile - ' . $this->escaper->escapeHtml($user->name));
    }

    /**
     * Allow to change your user settings
     */
    public function settingsAction()
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $user = Users::findFirstById($usersId);
        if (!$user) {
            $this->flashSession->error('The user does not exist');
            return $this->response->redirect();
        }

        if ($this->request->isPost()) {
            $user->timezone      = $this->request->getPost('timezone');
            $user->notifications = $this->request->getPost('notifications');
            if ($user->save()) {
                $this->session->get('timezone', $user->timezone);
                $this->flashSession->success('Settings were successfully updated');
                return $this->response->redirect();
            }
        } else {
            $this->tag->displayTo('timezone', $user->timezone);
            $this->tag->displayTo('notifications', $user->notifications);
        }

        $this->tag->setTitle('My Settings');
        $this->tag->setAutoEscape(false);

        $this->view->user      = $user;
        $this->view->timezones = APP_PATH .'/app/config/timezones.php';

        $parametersNumberPosts   = array(
            'users_id = ?0 AND deleted = 0',
            'bind' => array($user->id)
        );
        $this->view->numberPosts = Posts::count($parametersNumberPosts);

        $parametersNumberReplies   = array(
            'users_id = ?0',
            'bind' => array($user->id)
        );
        $this->view->numberReplies = PostsReplies::count($parametersNumberReplies);
    }

    /**
     *
     */
    public function helpAction()
    {
        $this->response->redirect('discussion/1/welcome-to-the-forum');
    }
}
