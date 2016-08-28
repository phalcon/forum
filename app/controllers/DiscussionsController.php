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

namespace Phosphorum\Controllers;

use Phalcon\Mvc\View;
use Phalcon\Http\Response;
use Phosphorum\Models\Karma;
use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;
use Phosphorum\Models\IrcLog;
use Phosphorum\Search\Indexer;
use Phosphorum\Models\PostsViews;
use Phosphorum\Models\PostsVotes;
use Phosphorum\Models\Categories;
use Phosphorum\Models\Activities;
use Phosphorum\Models\PostsReplies;
use Phalcon\Http\ResponseInterface;
use Phosphorum\Models\PostsBounties;
use Phosphorum\Models\TopicTracking;
use Phosphorum\Models\PostsPollVotes;
use Phosphorum\Models\PostsPollOptions;
use Phosphorum\Models\PostsSubscribers;
use Phosphorum\Mvc\Controllers\TokenTrait;
use Phosphorum\Models\ActivityNotifications;

/**
 * Class DiscussionsController
 *
 * @package Phosphorum\Controllers
 */
class DiscussionsController extends ControllerBase
{
    use TokenTrait;

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
     * This shows the create post form and also store the related post
     */
    public function createAction()
    {
        if (!$usersId = $this->session->get('identity')) {
            $this->flashSession->error('You must be logged first');
            $this->response->redirect();
            return;
        }

        $this->tag->setTitle('Start a Discussion');
        $this->gravatar->setSize(48);

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost('create-post')) {
                $this->response->redirect();
                return;
            }

            $title = $this->request->getPost('title', 'trim');

            $post                = new Posts();
            $post->users_id      = $usersId;
            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->slug->generate($title);
            $post->content       = $this->request->getPost('content');

            if ($post->save()) {
                $user = Users::findFirstById($usersId);

                if ($pollOptions = $this->request->getPost('pollOptions', ['trim'], [])) {
                    foreach ($pollOptions as $opt) {
                        $option = new PostsPollOptions();
                        $option->posts_id = $post->id;
                        $option->title = htmlspecialchars($opt, ENT_QUOTES);
                        $option->save();
                    }
                }

                $user->increaseKarma(Karma::ADD_NEW_POST);
                $user->save();

                $this->response->redirect("discussion/{$post->id}/{$post->slug}");
                return;
            }

            $this->flashSession->error(join('<br>', $post->getMessages()));
            $this->view->setVar('firstTime', false);
        } else {
            $this->view->setVar('firstTime', Posts::countByUsersId($usersId) == 0);
        }

        $this->view->setVar('categories', Categories::find(['order' => 'name']));
    }

    /**
     * Stick post.
     *
     * @param int $id Post ID
     * @return ResponseInterface
     */
    public function stickAction($id)
    {
        if (!$this->checkTokenGet('post-' . $id)) {
            return $this->response->redirect();
        }

        if (!$usersId = $this->session->get('identity')) {
            $this->flashSession->error('You must be logged first');
            $this->response->redirect();
            return $this->response->redirect();
        }

        $parameters = [
            "id = ?0 AND sticked = ?1 AND 'Y' = ?2",
            'bind' => [$id, Posts::IS_UNSTICKED, $this->session->get('identity-moderator')]
        ];

        if (!$post = Posts::findFirst($parameters)) {
            $this->flashSession->error('The discussion does not exist');
            $this->response->redirect();
            return $this->response->redirect();
        }

        if (Posts::IS_DELETED == $post->deleted) {
            $this->flashSession->error("The post is deleted");
            return $this->response->redirect();
        }

        $post->sticked = Posts::IS_STICKED;
        if ($post->save()) {
            $this->flashSession->success('Discussion was successfully sticked');
            return $this->response->redirect();
        }

        $this->flashSession->error(join('<br>', $post->getMessages()));
        return $this->response->redirect();
    }

    /**
     * Unstick post.
     *
     * @param int $id Post ID
     * @return ResponseInterface
     */
    public function unstickAction($id)
    {
        if (!$this->checkTokenGet('post-' . $id)) {
            return $this->response->redirect();
        }

        if (!$usersId = $this->session->get('identity')) {
            $this->flashSession->error('You must be logged first');
            $this->response->redirect();
            return $this->response->redirect();
        }

        $parameters = [
            "id = ?0 AND sticked = ?1 AND 'Y' = ?2",
            'bind' => [$id, Posts::IS_STICKED, $this->session->get('identity-moderator')]
        ];

        if (!$post = Posts::findFirst($parameters)) {
            $this->flashSession->error('The discussion does not exist');
            $this->response->redirect();
            return $this->response->redirect();
        }

        if (Posts::IS_DELETED == $post->deleted) {
            $this->flashSession->error("The post is deleted");
            return $this->response->redirect();
        }

        $post->sticked = Posts::IS_UNSTICKED;
        if ($post->save()) {
            $this->flashSession->success('Discussion was successfully unsticked');
            return $this->response->redirect();
        }

        $this->flashSession->error(join('<br>', $post->getMessages()));
        return $this->response->redirect();
    }

    /**
     * This shows the create post form and also store the related post
     *
     * @param int $id Post ID
     */
    public function editAction($id)
    {
        if (!$usersId = $this->session->get('identity')) {
            $this->flashSession->error('You must be logged first');
            $this->response->redirect();
            return;
        }

        $parameters = [
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            'bind' => [$id, $usersId, $this->session->get('identity-moderator')]
        ];

        if (!$post = Posts::findFirst($parameters)) {
            $this->flashSession->error('The discussion does not exist');
            $this->response->redirect();
            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost('edit-post-'.$id)) {
                $this->response->redirect();
                return;
            }

            $title   = $this->request->getPost('title', 'trim');
            $content = $this->request->getPost('content');

            /** @var \Phalcon\Db\Adapter\Pdo\Mysql $connection */
            $connection = $this->getDI()->getShared('db');
            $connection->begin();

            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = $this->slug->generate($title);
            $post->content       = $content;
            $post->edited_at     = time();

            if (!$post->hasPoll() || !$post->isStartVoting()) {
                foreach ($post->getPollOptions() as $option) {
                    $option->delete();
                }

                if ($pollOptions = $this->request->getPost('pollOptions', ['trim'], [])) {
                    foreach ($pollOptions as $opt) {
                        $option           = new PostsPollOptions();
                        $option->posts_id = $post->id;
                        $option->title    = htmlspecialchars($opt, ENT_QUOTES);
                        $option->save();
                    }
                }
            }

            if ($post->isStartVoting()) {
                $connection->rollback();
                $this->flashSession->error("The voting for the poll was started. You can't change the Poll.");
            } elseif ($post->save()) {
                if ($post->users_id != $usersId && $user = Users::findFirstById($usersId)) {
                    $user->increaseKarma(Karma::MODERATE_POST);
                    $user->save();
                }

                $connection->commit();
                $this->response->redirect("discussion/{$post->id}/{$post->slug}");
                return;
            } else {
                $connection->rollback();
                $this->flashSession->error(join('<br>', $post->getMessages()));
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
            'categories'   => Categories::find(['order' => 'name']),
            'post'         => $post,
            'optionsCount' => $post->pollOptions->count()
        ]);
    }

    /**
     * Deletes the Post
     *
     * @param int $id
     * @return ResponseInterface
     */
    public function deleteAction($id)
    {
        if (!$this->checkTokenGet('post-' . $id)) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $parameters = [
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            'bind' => [$id, $usersId, $this->session->get('identity-moderator')]
        ];

        if (!$post = Posts::findFirst($parameters)) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if (Posts::IS_DELETED == $post->deleted) {
            $this->flashSession->error("The post is already deleted");
            return $this->response->redirect();
        }

        if ($post->sticked == 'Y') {
            $this->flashSession->error("The discussion cannot be deleted because it's sticked");
            return $this->response->redirect();
        }

        $post->deleted = Posts::IS_DELETED;
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
     * @return ResponseInterface
     */
    public function subscribeAction($id)
    {
        if (!$this->checkTokenGet('post-' . $id)) {
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
     * @return ResponseInterface
     */
    public function unsubscribeAction($id)
    {
        if (!$this->checkTokenGet('post-' . $id)) {
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
     * @param string $slug Post slug [Optional]
     */
    public function viewAction($id, $slug = '')
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
            if (!$post = Posts::findFirstById($id)) {
                $this->flashSession->error('The discussion does not exist');
                $this->response->redirect();
                return;
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                $this->response->redirect();
                return;
            }

            $difference = $post->getDifference();
            $this->view->setVar('is_edited', !empty(trim($difference)));

            $ipAddress = $this->request->getClientAddress();

            $parameters = [
                'posts_id = ?0 AND ipaddress = ?1',
                'bind' => [$id, $ipAddress]
            ];

            // A view is stored by ip address
            if (!$viewed = PostsViews::count($parameters)) {
                // Increase the number of views in the post
                $post->number_views++;
                if ($post->users_id != $usersId) {
                    $post->user->increaseKarma(Karma::VISIT_ON_MY_POST);

                    if ($user = Users::findFirstById($usersId)) {
                        $user->increaseKarma($user->moderator == 'Y' ? Karma::MODERATE_VISIT_POST : Karma::VISIT_POST);
                        $user->save();
                    }
                }

                $postView            = new PostsViews();
                $postView->post      = $post;
                $postView->ipaddress = $ipAddress;
                if (!$postView->save()) {
                    $this->flash->error(join('<br>', $postView->getMessages()));
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
                'canonical' => "discussion/{$post->id}/{$post->slug}",
                'author'    => $post->user
            ]);
        } else {
            if (!$this->checkTokenPost('post-' . $id)) {
                $this->response->redirect();
                return;
            }

            if (!$usersId) {
                $this->flashSession->error('You must be logged in first to add a comment');
                $this->response->redirect();
                return;
            }

            // Find the post using POST
            if (!$post = Posts::findFirstById($this->request->getPost('id'))) {
                $this->flashSession->error('The discussion does not exist');
                $this->response->redirect();
                return;
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                $this->response->redirect();
                return;
            }

            if ($content = $this->request->getPost('content', 'trim')) {
                // Check if the question can have a bounty before add the reply
                $canHaveBounty = $post->canHaveBounty();

                if (!$user = Users::findFirstById($usersId)) {
                    $this->flashSession->error('You must be logged in first to add a comment');
                    $this->response->redirect();
                    return;
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
                            $this->flash->error(join('<br>', $postBounty->getMessages()));
                        }
                    }

                    $this->response->redirect("discussion/{$post->id}/{$post->slug}#C{$postReply->id}");
                    return;
                }

                $this->flash->error(join('<br>', $postReply->getMessages()));
            }
        }

        $voting = [];

        if ($post->hasPoll()) {
            $totalVotes = $post->getPollVotes()->count();
            $votesCount = PostsPollVotes::count(['posts_id = ?0', 'group' => 'options_id', 'bind' => [$post->id]]);

            foreach ($votesCount as $row) {
                /** @var \Phalcon\Mvc\Model\Row $row */
                $voting[$row->offsetGet('options_id')] = round($row->offsetGet('rowcount') * 100 / $totalVotes, 1);
            }
        }

        // Set the post name as title - escaping it first
        $this->tag->setTitle($this->escaper->escapeHtml($post->title) . ' - Discussion');

        $this->view->setVars([
            'post'   => $post,
            'voted'  => $post->isParticipatedInPoll($usersId),
            'voting' => $voting
        ]);
    }

    /**
     * Shows the latest modification made to a post
     *
     * @param int $id The Post id.
     */
    public function historyAction($id = 0)
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->view->setVar('difference', 'The discussion does not exist or it has been deleted.');
            return;
        }

        $this->view->setVar('difference', $post->getDifference() ?: 'No history available to show');
    }

    /**
     * Votes a post up
     *
     * @param int $id The post ID.
     * @return ResponseInterface
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson('post-' . $id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to vote for the post again.'
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
                    'message' => (string) $message->getMessage()
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
                        'message' => (string) $message->getMessage()
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
     *
     * @param int $id The post ID.
     * @return ResponseInterface
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson('post-' . $id)) {
            $csrfTokenError = [
                'status'  => 'error',
                'message' => 'This post is outdated. Please try to vote for the post again.'
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
            $responseContent = [
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            ];
            return $response->setJsonContent($responseContent);
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
     *
     * @return ResponseInterface
     */
    public function findRelatedAction()
    {
        $response = new Response();
        $indexer  = new Indexer();
        $results  = [];

        if ($this->request->has('title')) {
            if ($title = $this->request->getPost('title', 'trim')) {
                $results = $indexer->search(['title' => $title], 5);
            }
        }

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
