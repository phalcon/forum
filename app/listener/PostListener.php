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

namespace Phosphorum\Listener;

use Phalcon\Di;
use Phalcon\Events\Event;
use Phalcon\Db\Adapter\Pdo;
use Phosphorum\Model\Posts;
use Phosphorum\Model\Users;
use Aws\AwsClientInterface;
use Aws\Exception\AwsException;
use Phosphorum\Model\Activities;
use Phosphorum\Model\PostsViews;
use Phosphorum\Model\PostsHistory;
use Phosphorum\Model\Notifications;
use Phosphorum\Services\QueueService;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phosphorum\Model\PostsNotifications;
use Phosphorum\Discord\DiscordComponent;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Phosphorum\Listener\PostListener
 *
 * @package Phosphorum\Listener
 */
class PostListener
{
    public function beforeValidationOnCreate(Event $event, Posts $model)
    {
        $model->deleted         = 0;
        $model->number_views    = 0;
        $model->number_replies  = 0;
        $model->sticked         = $model::IS_UNSTICKED;
        $model->accepted_answer = 'N';
        $model->locked          = 'N';
        $model->status          = 'A';

        if ($model->title && !$model->slug) {
            $model->slug = $model->getDI()->getShared('slug')->generate($model->title);
        }
    }

    /**
     * Create a posts-views logging the ipaddress where the post was created
     * This avoids that the same session counts as post view
     *
     * @param Event $event
     * @param Posts $model
     */
    public function beforeCreate(Event $event, Posts $model)
    {
        $model->views = new PostsViews([
            'ipaddress' => $model->getDI()->getShared('request')->getClientAddress(),
        ]);
    }

    /**
     * TODO: Split this
     *
     * - Register a new activity
     * - Notify users that always want notifications
     * - Notify users that always want notifications
     * - Update the total of posts related to a category
     * - Queue notifications to be sent
     * - Send notification to the Discord
     * - Add new record to post_history table after create new post
     *
     * @param Event $event
     * @param Posts $model
     */
    public function afterCreate(Event $event, Posts $model)
    {
        if ($model->id <= 0) {
            return;
        }

        /**
         * Register the activity
         */
        $activity           = new Activities();
        $activity->users_id = $model->users_id;
        $activity->posts_id = $model->id;
        $activity->type     = Activities::NEW_POST;
        $activity->save();

        /**
         * Notify users that always want notifications
         */
        $notification           = new PostsNotifications();
        $notification->users_id = $model->users_id;
        $notification->posts_id = $model->id;
        $notification->save();

        /**
         * Notify users that always want notifications
         */
        $toNotify = [];
        foreach (Users::find(['notifications = "Y"', 'columns' => 'id']) as $user) {
            if ($model->users_id == $user->id) {
                continue;
            }

            $notification           = new Notifications();
            $notification->users_id = $user->id;
            $notification->posts_id = $model->id;
            $notification->type     = Notifications::TYPE_POST;
            $notification->save();

            $toNotify[$user->id] = $notification->id;
        }

        /**
         * Update the total of posts related to a category
         */
        $model->category->number_posts++;
        $model->category->save();

        /**
         * Queue notifications to be sent.
         * @var AwsClientInterface; $queue
         */
        if (!empty($toNotify)) {
            try {
                $queue = Di::getDefault()->get('queue');
                $queueName = (new QueueService())->getFullQueueName('notifications');
                $queue->sendMessage([
                    'DelaySeconds' => 1,
                    'MessageAttributes' => [
                        "Title" => [
                            'DataType' => "String",
                            'StringValue' => "Post notifications"
                        ],
                    ],
                    'MessageBody' => json_encode($toNotify),
                    'QueueUrl' => $queue->getQueueUrl(['QueueName' => $queueName])->get('QueueUrl'),
                ]);
            } catch (AwsException $e) {
                Di::getDefault()->get('logger')->error($e->getMessage());
            } catch (\Exception $e) {
                // Do nothing
            } catch (\Throwable $e) {
                // Do nothing
            }
        }

        /** @var DiscordComponent $discord */
        try {
            $discord = container('discord');
            $discord->addMessageAboutDiscussion($model);
        } catch (\Exception $e) {
            // Do nothing
        } catch (\Throwable $e) {
            // Do nothing
        }

        /**
         * Add new record to post_history table after create new post
         */
        $this->addPostHistory($model);
    }

    /**
     * Database queries listener.
     *
     * @param Event $event
     * @param Posts $model
     */
    public function afterSave(Event $event, Posts $model)
    {
        if ($this->hasDifferenceWithHistory($model)) {
            $this->addPostHistory($model);
        }
    }

    public function afterDelete(Event $event, Posts $model)
    {
        $model->clearCache();
    }

    /**
     * Add new record to post_history table after create new post
     *
     * @param Posts $model
     */
    protected function addPostHistory(Posts $model)
    {
        $model->clearCache();

        if (!container()->has('session')) {
            return;
        }

        if (!container('session')->isStarted()) {
            return;
        }

        // In case of updating post through creating PostsViews
        if (!container('session')->has('identity')) {
            return;
        }

        $history = new PostsHistory([
            'posts_id' => $model->id,
            'users_id' => container('session')->get('identity'),
            'content'  => $model->content,
        ]);

        if (!$history->save()) {
            $reason   = [];

            foreach ($history->getMessages() as $message) {
                /** @var \Phalcon\Mvc\Model\MessageInterface $message */
                $reason[] = $message->getMessage();
            }

            container('logger')->error('Unable to store post history. Post id: {id}. Reason: {reason}', [
                'id'     => $model->id,
                'reason' => implode('. ', $reason)
            ]);
        }
    }

    /**
     * Get difference between last post in table post_history and received data
     *
     * @param Posts $model
     *
     * @return bool
     */
    protected function hasDifferenceWithHistory(Posts $model)
    {
        $history = PostsHistory::findLast($model);

        if (!$history->valid()) {
            return false;
        }

        if ($history->count() > 1) {
            $history = $history->offsetGet(1);
        } else {
            $history = $history->getFirst();
        }

        /**
         * Checking difference in post's content
         *
         * @var ResultsetInterface|Simple $history
         */
        if ((strcmp($model->content, $history->content)) != 0) {
            return true;
        }

        /** Checking difference in post's title*/
        /*
         * @todo this checking should be implement when table post_history will have title of post, column `title`
         */
//        if ((strcmp($this->title, $history->title)) != 0) {
//            return true;
//        }

        return false;
    }
}
