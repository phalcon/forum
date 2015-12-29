<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2015 Phalcon Team and contributors                  |
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
use Phosphorum\Models\PostsSubscribers;
use Phosphorum\Models\Categories;
use Phosphorum\Models\Activities;
use Phosphorum\Models\ActivityNotifications;
use Phosphorum\Models\IrcLog;
use Phosphorum\Models\Users;
use Phosphorum\Models\Karma;
use Phosphorum\Models\TopicTracking;
use Phosphorum\Search\Indexer;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Phosphorum\Mvc\Controllers\TokenTrait;

/**
 * Class DiscussionsController
 *
 * @package Phosphorum\Controllers
 */
class DiscussionsController extends ControllerBase
{
    use TokenTrait;

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
        $this->view->limitPost  = self::POSTS_IN_PAGE;
    }

    /**
     * This method prepares the queries to be executed in each list of posts
     * The returned builders are used as base in the search, tagged list and index lists
     *
     * @param bool $joinReply
     * @return array
     */
    protected function prepareQueries($joinReply = false)
    {
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        $itemBuilder = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Models\Posts'])
            ->orderBy('p.sticked DESC, p.created_at DESC');

        if ($joinReply) {
            $itemBuilder
                ->groupBy('p.id')
                ->join('Phosphorum\Models\PostsReplies', 'r.posts_id = p.id', 'r');
        }

        $totalBuilder = clone $itemBuilder;

        $itemBuilder
            ->columns(['p.*'])
            ->limit(self::POSTS_IN_PAGE);

        $totalBuilder
            ->columns('COUNT(*) AS count');

        return [$itemBuilder, $totalBuilder];
    }

    /**
     * Shows latest posts using an order clause
     *
     * @param string $order
     * @param int  $offset
     */
    public function indexAction($order = null, $offset = 0)
    {
        /**
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder
         */
        list($itemBuilder, $totalBuilder) = $this->prepareQueries($order == "answers");

        /**
         * Create the conditions according to the parameter order
         */
        $userId = $this->session->get('identity');
        $readposts = [];

        if ($userId != '') {
            $ur = TopicTracking::findFirst("user_id='".$userId."'");
            if ($ur !== false) {
                $readposts = explode(",", $ur->topic_id);
            }
        }

        $params = null;
        switch ($order) {

            case 'hot':
                $this->tag->setTitle('Hot Discussions');
                $itemBuilder->orderBy('p.modified_at DESC');
                break;

            case 'my':
                $this->tag->setTitle('My Discussions');
                if ($userId) {
                    $params       = [$userId];
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
                    $params            = [$userId];
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

        $order = $order ?: 'new';
        $this->view->setVars([
            'logged'       => $userId,
            'readposts'    => $readposts,
            'posts'        => $itemBuilder->getQuery()->execute($params),
            'totalPosts'   => $totalBuilder->getQuery()->setUniqueRow(true)->execute($params),
            'currentOrder' => $order,
            'offset'       => $offset,
            'paginatorUri' => "discussions/{$order}",
            'canonical'    => ''
        ]);
    }

    /**
     * Shows latest posts by category
     *
     * @param int $categoryId Category Id
     * @param string $slug Category Slug
     * @param int $offset Posts offset
     * @return \Phalcon\Http\ResponseInterface
     */
    public function categoryAction($categoryId, $slug, $offset = 0)
    {
        if (!$category = Categories::findFirstById($categoryId)) {
            $this->flashSession->notice("The category doesn't exist");
            $this->logger->error("The category doesn't exist");
            return $this->response->redirect();
        }

        $this->tag->setTitle('Discussions');
        $readposts = [];

        if ($userId = $this->session->get('identity')) {
            $ur = TopicTracking::findFirst(['user_id= ?0', 'bind' => [$userId]]);
            $readposts = $ur ? explode(',', $ur->topic_id) : [];
        }

        /**
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder
         * @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder
         */
        list($itemBuilder, $totalBuilder) = $this->prepareQueries();

        $totalBuilder->where('p.categories_id = ?0 AND p.deleted = 0');

        $posts = $itemBuilder
            ->where('p.categories_id = ?0 AND p.deleted = 0')
            ->orderBy('p.created_at DESC')
            ->offset((int)$offset)
            ->getQuery()
            ->execute([$categoryId]);

        if (!count($posts)) {
            $this->flashSession->notice('There are no posts in category: ' . $category->name);
            return $this->response->redirect();
        }

        $totalPosts = $totalBuilder
            ->getQuery()
            ->setUniqueRow(true)
            ->execute([$categoryId]);

        $this->view->setVars([
            'readposts'    => $readposts,
            'posts'        => $posts,
            'totalPosts'   => $totalPosts,
            'currentOrder' => null,
            'offset'       => (int)$offset,
            'paginatorUri' => 'category/' . $category->id . '/' . $category->slug,
            'logged'       => $userId
        ]);
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function createAction()
    {
        if (!$usersId = $this->session->get('identity')) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $this->tag->setTitle('Start a Discussion');
        $this->gravatar->setSize(48);

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $title = $this->request->getPost('title', 'trim');

            /** @var Users $user */
            $user = Users::findFirstById($usersId);
            $user->increaseKarma(Karma::ADD_NEW_POST);
            $user->save();

            $post                = new Posts();
            $post->users_id      = $usersId;
            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->slug->generate($title);
            $post->content       = $this->request->getPost('content');

            if ($post->save()) {
                return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
            }

            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->view->setVar('firstTime', false);

        } else {
            $this->view->setVar('firstTime', Posts::countByUsersId($usersId) == 0);
        }

        $parameters = ['order' => 'name'];

        $this->view->setVar('categories', Categories::find($parameters));
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
        $parameters = [
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            'bind' => [$id, $usersId, $this->session->get('identity-moderator')]
        ];
        $post = Posts::findFirst($parameters);

        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $title   = $this->request->getPost('title', 'trim');
            $content = $this->request->getPost('content');

            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->slug->generate($title);
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
                return $this->response->redirect("discussion/{$post->id}/{$post->slug}");
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

        $this->gravatar->setSize(48);

        $this->view->setVars([
            'categories' => Categories::find(['order' => 'name']),
            'post'       => $post
        ]);
    }

    /**
     * Deletes the Post
     *
     * @param int $id
     * @return Response|\Phalcon\Http\ResponseInterface
     */
    public function deleteAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        /**
         * Find the post using get
         */
        $parameters = [
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            'bind' => [$id, $usersId, $this->session->get('identity-moderator')]
        ];

        if (!$post = Posts::findFirst($parameters)) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($post->deleted == 'Y') {
            $this->flashSession->error("The post is already deleted");
            return $this->response->redirect();
        }

        if ($post->sticked == 'Y') {
            $this->flashSession->error("The discussion cannot be deleted because it's sticked");
            return $this->response->redirect();
        }

        $post->deleted = 1;
        if ($post->save()) {
            $usersId = $this->session->get('identity');
            if ($post->users_id != $usersId) {
                /** @var Users $user */
                if ($user = Users::findFirstById($usersId)) {
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

        $this->flashSession->error(join('<br>', $post->getMessages()));
        return $this->response->redirect();
    }

    /**
     * Subscribe to a post to receive e-mail notifications
     *
     * @param string $id
     * @return Response
     */
    public function subscribeAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $subscription = PostsSubscribers::findFirst([
            'posts_id = ?0 AND users_id = ?1',
            'bind' => [$post->id, $usersId]
        ]);
        if (!$subscription) {
            $subscription             = new PostsSubscribers();
            $subscription->posts_id   = $post->id;
            $subscription->users_id   = $usersId;
            $subscription->created_at = time();
            if ($subscription->save()) {
                $this->flashSession->notice('You are now subscribed to this post');
            }
        }

        return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
    }

    /**
     * Unsubscribe from a post of receiving e-mail notifications
     *
     * @param string $id
     * @return Response
     */
    public function unsubscribeAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $subscription = PostsSubscribers::findFirst([
            'posts_id = ?0 AND users_id = ?1',
            'bind' => [$post->id, $usersId]
        ]);
        if ($subscription) {
            $this->flashSession->notice('You were successfully unsubscribed from this post');
            $subscription->delete();
        }

        return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
    }

    /**
     * Displays a post and its comments
     *
     * @param int $id Post ID
     * @param string $slug Post slug
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function viewAction($id, $slug)
    {
        $id = (int)$id;

        // Check read / unread topic
        if ($usersId = $this->session->get('identity')) {
            $check_topic = new TopicTracking();
            $check_topic->user_id = $usersId;
            $check_topic->topic_id = $id;

            if ($check_topic->create() == false) {
                $check_topic->updateTracking($id, $usersId);
            }
        }

        $this->gravatar->setSize(48);

        if (!$this->request->isPost()) {
            // Find the post using get
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

            $parameters = [
                'posts_id = ?0 AND ipaddress = ?1',
                'bind' => [$id, $ipAddress]
            ];

            $viewed = PostsViews::count($parameters);

            // A view is stored by ip address
            if (!$viewed) {
                // Increase the number of views in the post
                $post->number_views++;
                if ($post->users_id != $usersId) {

                    $post->user->increaseKarma(Karma::VISIT_ON_MY_POST);

                    if ($usersId > 0) {
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
                // Enable cache
                $this->view->cache(['key' => 'post-' . $id]);

                // Check for a cache
                if ($this->viewCache->exists('post-' . $id)) {
                    return;
                }
            }

            // Generate canonical meta
            $this->view->setVars([
                'canonical' => 'discussion/' . $post->id . '/' . $post->slug,
                'author'    => $post->user
            ]);
        } else {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            if (!$usersId) {
                $this->flashSession->error('You must be logged in first to add a comment');
                return $this->response->redirect();
            }

            // Find the post using POST
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

                // Check if the question can have a bounty before add the reply
                $canHaveBounty = $post->canHaveBounty();

                $user = Users::findFirstById($usersId);
                if (!$user) {
                    $this->flashSession->error('You must be logged in first to add a comment');
                    return $this->response->redirect();
                }

                // Only update the number of replies if the user that commented isn't the same that posted
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

        $this->view->setVar('post', $post);
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
        $parameters    = ['posts_id = ?0', 'bind' => [$post->id], 'order' => 'created_at DESC'];

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

            $diff     = new \Diff($b, $a, []);
            $renderer = new \Diff_Renderer_Html_SideBySide();

            echo $diff->Render($renderer);
        } else {
            $this->flash->notice('No history available to show');
        }
    }

    /**
     * Votes a post up
     *
     * @param int $id Post ID
     * @return Response
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Post does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentlogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = [
                'status'  => 'error',
                'message' => "You don't have enough votes available"
            ];
            return $response->setJsonContent($contentDontHave);
        }

        if (PostsVotes::count(['posts_id = ?0 AND users_id = ?1', 'bind' => [$post->id, $user->id]])) {
            $contentAlreadyVote = [
                'status'  => 'error',
                'message' => 'You have already voted this post'
            ];
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        $postVote->vote     = PostsVotes::VOTE_UP;
        if (!$postVote->save()) {
            foreach ($postVote->getMessages() as $message) {
                $contentError = [
                    'status'  => 'error',
                    'message' => $message->getMessage()
                ];
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
                    $contentErrorSave = [
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    ];
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        if ($post->users_id != $user->id) {
            $activity                       = new ActivityNotifications();
            $activity->users_id             = $post->users_id;
            $activity->posts_id             = $post->id;
            $activity->posts_replies_id     = null;
            $activity->users_origin_id      = $user->id;
            $activity->type                 = 'P';
            $activity->save();
        }

        $contentOk = [
            'status' => 'OK'
        ];

        return $response->setJsonContent($contentOk);
    }

    /**
     * Votes a post down
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            ];
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = [
                'status'  => 'error',
                'message' => 'Post does not exist'
            ];
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($contentlogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = [
                'status'  => 'error',
                'message' => "You don't have enough votes available"
            ];
            return $response->setJsonContent($contentDontHave);
        }

        if (PostsVotes::count(['posts_id = ?0 AND users_id = ?1', 'bind' => [$post->id, $user->id]])) {
            $contentAlreadyVote = [
                'status'  => 'error',
                'message' => 'You have already voted this post'
            ];
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        $postVote->vote     = PostsVotes::VOTE_DOWN;
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
                    $contentErrorSave = [
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    ];
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        $contentOk = [
            'status' => 'OK'
        ];
        return $response->setJsonContent($contentOk);
    }

    /**
     * Shows the latest activity on the IRC
     */
    public function ircAction()
    {
        $parameters = [
            'order' => 'datelog DESC',
            'limit' => 250
        ];
        $ircLog = IrcLog::find($parameters);

        $activities = [];
        foreach ($ircLog as $log) {
            $who          = explode('@', $log->who);
            $parts        = explode('!', $who[0]);
            $log->who     = substr($parts[0], 1);
            $activities[] = $log;
        }

        $this->view->setVar('activities', array_reverse($activities));

        $this->tag->setTitle('Recent Activity on the IRC');
    }

    /**
     * Shows the latest activity on the forum
     */
    public function activityAction($offset = 0)
    {
        $parameters = [
            'order' => 'created_at DESC',
            'limit' => ['number' => self::POSTS_IN_PAGE, 'offset' => 0]
        ];

        $this->view->setVars([
            'total'      => Activities::count(),
            'activities' => Activities::find($parameters),
        ]);

        $this->tag->setTitle('Recent Activity on the Forum');
    }

    /**
     * Perform the search of posts only searching in the title
     */
    public function searchAction()
    {

        $this->tag->setTitle('Search Results');

        $q = $this->request->getQuery('q');

        $indexer = new Indexer();

        $posts = $indexer->search(['title' => $q, 'content' => $q], 50, true);
        if (!count($posts)) {
            $posts = $indexer->search(['title' => $q], 50, true);
            if (!count($posts)) {
                $this->flashSession->notice('There are no search results');
                return $this->response->redirect();
            }
        }

        $paginator = new \stdClass;
        $paginator->count = 0;

        $this->view->setVars([
            'posts'        => $posts,
            'totalPosts'   => $paginator,
            'currentOrder' => null,
            'offset'       => 0,
            'paginatorUri' => 'search'
        ]);
    }

    /**
     * Reload categories
     * @todo Move to the CategoriesController
     */
    public function reloadCategoriesAction()
    {
        $this->view->setVar('categories', Categories::find(['order' => 'number_posts DESC, name']));

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->getCache()->delete('sidebar');
    }

    /**
     * Shows the user profile
     *
     * @todo Move to the UsersController
     * @param $id
     * @param $username
     */
    public function userAction($id, $username)
    {
        $user = $id ? Users::findFirstById($id) : Users::findFirstByLogin($username);
        if (!$user) {
            $user = Users::findFirstByName($username);
        }

        if (!$user) {
            $this->flashSession->error('The user does not exist');
            $this->response->redirect();
            return;
        }

        $this->view->setVar('user', $user);

        $parametersNumberPosts = [
            'users_id = ?0 AND deleted = 0',
            'bind' => [$user->id]
        ];
        $this->view->setVar('numberPosts', Posts::count($parametersNumberPosts));

        $parametersNumberReplies = [
            'users_id = ?0',
            'bind' => [$user->id]
        ];
        $this->view->setVar('numberReplies', PostsReplies::count($parametersNumberReplies));

        $parametersActivities = [
            'users_id = ?0',
            'bind'  => [$user->id],
            'order' => 'created_at DESC',
            'limit' => 15
        ];
        $this->view->setVar('activities', Activities::find($parametersActivities));

        $users   = Users::find(['columns' => 'id', 'conditions' => 'karma != 0', 'order' => 'karma DESC']);
        $ranking = count($users);
        foreach ($users as $position => $everyUser) {
            if ($everyUser->id == $user->id) {
                $ranking = $position + 1;
                break;
            }
        }

        $this->view->setVars([
            'ranking'       => $ranking,
            'total_ranking' => count($users),
        ]);

        $this->gravatar->setSize(64);
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
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $user->timezone      = $this->request->getPost('timezone');
            $user->notifications = $this->request->getPost('notifications');
            $user->theme         = $this->request->getPost('theme');
            $user->digest        = $this->request->getPost('digest');
            if ($user->save()) {
                $this->session->set('identity-theme', $user->theme);
                $this->session->get('identity-timezone', $user->timezone);
                $this->flashSession->success('Settings were successfully updated');
                return $this->response->redirect();
            }

        } else {
            $this->tag->displayTo('timezone', $user->timezone);
            $this->tag->displayTo('notifications', $user->notifications);
            $this->tag->displayTo('theme', $user->theme);
            $this->tag->displayTo('digest', $user->digest);
        }

        $this->tag->setTitle('My Settings');
        $this->tag->setAutoEscape(false);

        $this->view->user      = $user;
        $this->view->timezones = require APP_PATH .'/app/config/timezones.php';

        $parametersNumberPosts = [
            'users_id = ?0 AND deleted = 0',
            'bind' => [$user->id]
        ];
        $this->view->numberPosts = Posts::count($parametersNumberPosts);

        $parametersNumberReplies = [
            'users_id = ?0',
            'bind' => [$user->id]
        ];

        $this->gravatar->setSize(64);
        $this->view->numberReplies = PostsReplies::count($parametersNumberReplies);
    }

    /**
     * Shows the latest notifications for the current user
     */
    public function notificationsAction($offset = 0)
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

        $this->view->user = $user;

        $this->view->notifications = ActivityNotifications::find([
            'users_id = ?0',
            'bind'  => [$usersId],
            'limit' => 128,
            'order' => 'created_at DESC'
        ]);

        $this->tag->setTitle('Notifications');
    }

    /**
     * Finds related posts
     */
    public function findRelatedAction()
    {
        $response = new Response();

        $indexer = new Indexer();
        $results = $indexer->search(['title' => $this->request->getPost('title')], 5);

        $contentOk = [
            'status'  => 'OK',
            'results' => $results
        ];
        return $response->setJsonContent($contentOk);
    }

    /**
     * Finds related posts
     */
    public function showRelatedAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $post = Posts::findFirstById($this->request->getPost('id'));
        if ($post) {
            $indexer = new Indexer();
            $posts = $indexer->search(
                [
                    'title'    => $post->title,
                    'category' => $post->categories_id
                ],
                5,
                true
            );

            if (count($posts) == 0) {
                $posts = $indexer->search(['title' => $post->title], 5, true);
            }
            $this->view->setVar('posts', $posts);
        } else {
            $this->view->setVar('posts', []);
        }
    }
}
