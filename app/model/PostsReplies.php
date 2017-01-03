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

namespace Phosphorum\Model;

use Phalcon\Diff;
use Phalcon\Mvc\Model;
use Phalcon\Diff\Renderer\Html\SideBySide;
use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Class PostsReplies
 *
 * @property \Phosphorum\Model\Posts        post
 * @property \Phosphorum\Model\PostsReplies postReplyTo
 * @property \Phosphorum\Model\Users        user
 *
 * @method static PostsReplies findFirstById(int $id)
 * @method static PostsReplies findFirst($parameters = null)
 * @method static PostsReplies[] find($parameters = null)
 *
 * @package Phosphorum\Model
 */
class PostsReplies extends Model
{

    public $id;

    public $posts_id;

    public $users_id;

    public $in_reply_to_id;

    public $content;

    public $created_at;

    public $modified_at;

    public $edited_at;

    public $votes_up;

    public $votes_down;

    public $accepted;

    public function initialize()
    {
        $this->belongsTo(
            'posts_id',
            'Phosphorum\Model\Posts',
            'id',
            [
                'alias'    => 'post',
                'reusable' => true
            ]
        );

        $this->belongsTo(
            'in_reply_to_id',
            'Phosphorum\Model\PostsReplies',
            'id',
            [
                'alias'    => 'postReplyTo',
                'reusable' => true
            ]
        );

        $this->belongsTo(
            'users_id',
            'Phosphorum\Model\Users',
            'id',
            [
                'alias'    => 'user',
                'reusable' => true
            ]
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field' => 'created_at'
                ],
                'beforeUpdate' => [
                    'field' => 'modified_at'
                ]
            ])
        );
    }

    public function beforeCreate()
    {
        if ($this->in_reply_to_id > 0) {
            $postReplyTo = self::findFirst(['id = ?0', 'bind' => [$this->in_reply_to_id]]);
            if (!$postReplyTo) {
                $this->in_reply_to_id = 0;
            } elseif ($postReplyTo->posts_id != $this->posts_id) {
                $this->in_reply_to_id = 0;
            }
        }
        $this->accepted = 'N';
    }

    public function afterCreate()
    {
        if ($this->id > 0) {
            $activity           = new Activities();
            $activity->users_id = $this->users_id;
            $activity->posts_id = $this->posts_id;
            $activity->type     = Activities::NEW_REPLY;
            $activity->save();

            $toNotify = [];

            /**
             * Notify users that always want notifications
             */
            foreach (Users::find(['notifications = "Y"', 'columns' => 'id']) as $user) {
                if ($this->users_id != $user->id) {
                    $notification                   = new Notifications();
                    $notification->users_id         = $user->id;
                    $notification->posts_id         = $this->posts_id;
                    $notification->posts_replies_id = $this->id;
                    $notification->type             = Notifications::TYPE_COMMENT;
                    $notification->save();

                    $activity                       = new ActivityNotifications();
                    $activity->users_id             = $user->id;
                    $activity->posts_id             = $this->posts_id;
                    $activity->posts_replies_id     = $this->id;
                    $activity->users_origin_id      = $this->users_id;
                    $activity->type                 = 'C';
                    $activity->save();

                    $toNotify[$user->id] = $notification->id;
                }
            }

            /**
             * Register users subscribed to the post
             */
            foreach (PostsSubscribers::findByPostsId($this->posts_id) as $subscriber) {
                if (!isset($toNotify[$subscriber->users_id])) {
                    $notification                   = new Notifications();
                    $notification->users_id         = $subscriber->users_id;
                    $notification->posts_id         = $this->posts_id;
                    $notification->posts_replies_id = $this->id;
                    $notification->type             = Notifications::TYPE_COMMENT;
                    $notification->save();

                    $activity                       = new ActivityNotifications();
                    $activity->users_id             = $subscriber->users_id;
                    $activity->posts_id             = $this->posts_id;
                    $activity->posts_replies_id     = $this->id;
                    $activity->users_origin_id      = $this->users_id;
                    $activity->type                 = 'C';
                    $activity->save();

                    $toNotify[$subscriber->users_id] = $notification->id;
                }
            }

            /**
             * Register the user in the post's notifications
             */
            if (!isset($toNotify[$this->users_id])) {
                $parameters       = [
                    'users_id = ?0 AND posts_id = ?1',
                    'bind' => [$this->users_id, $this->posts_id]
                ];
                $hasNotifications = PostsNotifications::count($parameters);

                if (!$hasNotifications) {
                    $notification           = new PostsNotifications();
                    $notification->users_id = $this->users_id;
                    $notification->posts_id = $this->posts_id;
                    $notification->save();
                }
            }

            /**
             * Notify users that have commented in the same post
             */
            $postsNotifications = PostsNotifications::findByPostsId($this->posts_id);
            foreach ($postsNotifications as $postNotification) {
                if (!isset($toNotify[$postNotification->users_id])) {
                    if ($postNotification->users_id != $this->users_id) {

                        /**
                         * Generate an e-mail notification
                         */
                        $notification                   = new Notifications();
                        $notification->users_id         = $postNotification->users_id;
                        $notification->posts_id         = $this->posts_id;
                        $notification->posts_replies_id = $this->id;
                        $notification->type             = Notifications::TYPE_COMMENT;
                        $notification->save();

                        $activity                       = new ActivityNotifications();
                        $activity->users_id             = $postNotification->users_id;
                        $activity->posts_id             = $this->posts_id;
                        $activity->posts_replies_id     = $this->id;
                        $activity->users_origin_id      = $this->users_id;
                        $activity->type                 = 'C';
                        $activity->save();

                        $toNotify[$postNotification->users_id] = $notification->id;
                    }
                }
            }

            /**
             * Queue notifications to be sent
             */
            $this->getDI()->getShared('queue')->put($toNotify);
        }
    }

    public function afterSave()
    {
        $this->clearCache();

        $history                   = new PostsRepliesHistory();
        $history->posts_replies_id = $this->id;
        $usersId                   = $this->getDI()->getSession()->get('identity');
        $history->users_id         = $usersId ? $usersId : $this->users_id;
        $history->content          = $this->content;

        $history->save();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * @return bool|string
     */
    public function getHumanCreatedAt()
    {
        $diff = time() - $this->created_at;
        if ($diff > (86400 * 30)) {
            return date('M \'y', $this->created_at);
        } else {
            if ($diff > 86400) {
                return ((int)($diff / 86400)) . 'd ago';
            } else {
                if ($diff > 3600) {
                    return ((int)($diff / 3600)) . 'h ago';
                } else {
                    return ((int)($diff / 60)) . 'm ago';
                }
            }
        }
    }

    /**
     * @return bool|string
     */
    public function getHumanEditedAt()
    {
        $diff = time() - $this->edited_at;
        if ($diff > (86400 * 30)) {
            return date('M \'y', $this->edited_at);
        } else {
            if ($diff > 86400) {
                return ((int)($diff / 86400)) . 'd ago';
            } else {
                if ($diff > 3600) {
                    return ((int)($diff / 3600)) . 'h ago';
                } else {
                    return ((int)($diff / 60)) . 'm ago';
                }
            }
        }
    }

    public function clearCache()
    {
        if ($this->id) {
            $viewCache = $this->getDI()->getShared('viewCache');
            $viewCache->delete('post-' . $this->posts_id);
            $viewCache->delete('post-body-' . $this->posts_id);
            $viewCache->delete('post-users-' . $this->posts_id);
            $viewCache->delete('reply-body-' . $this->id);
        }
    }

    public function getDifference()
    {
        $history = PostsRepliesHistory::findLast($this);

        if (!$history->valid()) {
            return false;
        }

        if ($history->count() > 1) {
            $history = $history->offsetGet(1);
        } else {
            $history = $history->getFirst();
        }

        /** @var PostsRepliesHistory $history */

        $b = explode("\n", $history->content);

        $diff = new Diff($b, explode("\n", $this->content), []);
        $difference = $diff->render(new SideBySide);

        return $difference;
    }
}
