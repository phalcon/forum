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
use Aws\AwsClientInterface;
use Phosphorum\Model\Users;
use Aws\Exception\AwsException;
use Phosphorum\Model\Activities;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Model\Notifications;
use Phosphorum\Model\PostsSubscribers;
use Phosphorum\Model\PostsNotifications;
use Phosphorum\Discord\DiscordComponent;
use Phosphorum\Model\PostsRepliesHistory;
use Phosphorum\Model\ActivityNotifications;

/**
 * Phosphorum\Listener\PostRepliesListener
 *
 * @package Phosphorum\Listener
 */
class PostRepliesListener
{
    public function beforeCreate(Event $event, PostsReplies $model)
    {
        if ($model->in_reply_to_id > 0) {
            $postReplyTo = PostsReplies::findFirst(['id = ?0', 'bind' => [$model->in_reply_to_id]]);
            if (!$postReplyTo || $postReplyTo->posts_id != $model->posts_id) {
                $model->in_reply_to_id = 0;
            }
        }

        $model->accepted = 'N';
    }

    /**
     * @todo Split all functions below
     */
    public function afterCreate(Event $event, PostsReplies $model)
    {
        if ($model->id <= 0) {
            return;
        }
        $activity           = new Activities();
        $activity->users_id = $model->users_id;
        $activity->posts_id = $model->posts_id;
        $activity->type     = Activities::NEW_REPLY;
        $activity->save();

        $toNotify = [];

        /**
         * Notify users that always want notifications
         */
        foreach (Users::find(['notifications = "Y"', 'columns' => 'id']) as $user) {
            if ($model->users_id == $user->id) {
                continue;
            }

            $notification                   = new Notifications();
            $notification->users_id         = $user->id;
            $notification->posts_id         = $model->posts_id;
            $notification->posts_replies_id = $model->id;
            $notification->type             = Notifications::TYPE_COMMENT;
            $notification->save();

            $activity                       = new ActivityNotifications();
            $activity->users_id             = $user->id;
            $activity->posts_id             = $model->posts_id;
            $activity->posts_replies_id     = $model->id;
            $activity->users_origin_id      = $model->users_id;
            $activity->type                 = 'C';
            $activity->save();

            $toNotify[$user->id] = $notification->id;
        }

        /**
         * Register users subscribed to the post
         */
        foreach (PostsSubscribers::findByPostsId($model->posts_id) as $subscriber) {
            if (isset($toNotify[$subscriber->users_id])) {
                continue;
            }

            $notification                   = new Notifications();
            $notification->users_id         = $subscriber->users_id;
            $notification->posts_id         = $model->posts_id;
            $notification->posts_replies_id = $model->id;
            $notification->type             = Notifications::TYPE_COMMENT;
            $notification->save();

            $activity                       = new ActivityNotifications();
            $activity->users_id             = $subscriber->users_id;
            $activity->posts_id             = $model->posts_id;
            $activity->posts_replies_id     = $model->id;
            $activity->users_origin_id      = $model->users_id;
            $activity->type                 = 'C';
            $activity->save();

            $toNotify[$subscriber->users_id] = $notification->id;
        }

        /**
         * Register the user in the post's notifications
         */
        if (!isset($toNotify[$model->users_id])) {
            $parameters       = [
                'users_id = ?0 AND posts_id = ?1',
                'bind' => [$model->users_id, $model->posts_id]
            ];

            if (!PostsNotifications::count($parameters)) {
                $notification           = new PostsNotifications();
                $notification->users_id = $model->users_id;
                $notification->posts_id = $model->posts_id;
                $notification->save();
            }
        }

        /**
         * Notify users that have commented in the same post
         */
        $postsNotifications = PostsNotifications::findByPostsId($model->posts_id);
        foreach ($postsNotifications as $postNotification) {
            if (!isset($toNotify[$postNotification->users_id]) && $postNotification->users_id != $model->users_id) {
                /**
                 * Generate an e-mail notification
                 */
                $notification                   = new Notifications();
                $notification->users_id         = $postNotification->users_id;
                $notification->posts_id         = $model->posts_id;
                $notification->posts_replies_id = $model->id;
                $notification->type             = Notifications::TYPE_COMMENT;
                $notification->save();

                $activity                       = new ActivityNotifications();
                $activity->users_id             = $postNotification->users_id;
                $activity->posts_id             = $model->posts_id;
                $activity->posts_replies_id     = $model->id;
                $activity->users_origin_id      = $model->users_id;
                $activity->type                 = 'C';
                $activity->save();

                $toNotify[$postNotification->users_id] = $notification->id;
            }
        }

        /**
         * Queue notifications to be sent.
         *
         * @var AwsClientInterface; $queue
         */
        if (!empty($toNotify)) {
            try {
                $queue = Di::getDefault()->get('queue');
                $queue->sendMessage([
                    'DelaySeconds' => 1,
                    'MessageAttributes' => [
                        "Title" => [
                            'DataType' => "String",
                            'StringValue' => "Post replies notifications"
                        ],
                    ],
                    'MessageBody' => json_encode($toNotify),
                    'QueueUrl' => $queue->getQueueUrl(['QueueName' => 'notifications'])->get('QueueUrl'),
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
            $discord->addMessageAboutReply($model);
        } catch (\Exception $e) {
            // Do nothing
        } catch (\Throwable $e) {
            // Do nothing
        }
    }

    public function afterSave(Event $event, PostsReplies $model)
    {
        $model->clearCache();

        $usersId = $model->users_id;
        if (container()->has('session') && container('session')->isStarted() && container('session')->has('identity')) {
            $usersId = container('session')->get('identity');
        }

        $history                   = new PostsRepliesHistory();
        $history->posts_replies_id = $model->id;
        $history->users_id         = $usersId;
        $history->content          = $model->content;

        $history->save();
        if ($model->hasUpdated('accepted') && $model->accepted == 'Y') {
            /** @var DiscordComponent $discord */
            try {
                $discord = container('discord');
                $discord->addMessageAboutSolvedDiscussion($model);
            } catch (\Exception $e) {
                // Do nothing
            } catch (\Throwable $e) {
                // Do nothing
            }
        }
    }

    public function afterDelete(Event $event, PostsReplies $model)
    {
        $model->clearCache();
    }
}
