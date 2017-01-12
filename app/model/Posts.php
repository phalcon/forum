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

use DateTime;
use DateTimeZone;
use Phalcon\Diff;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Diff\Renderer\Html\SideBySide;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Class Posts
 *
 * @property Users user
 * @property Categories category
 * @property Simple replies
 * @property Simple views
 * @property Simple pollOptions
 * @property Simple pollVotes
 *
 * @method static Posts findFirstById(int $id)
 * @method static Posts findFirstByCategoriesId(int $id)
 * @method static Simple findByCategoriesId(int $id)
 * @method static Posts findFirst($parameters = null)
 * @method static Posts[] find($parameters = null)
 * @method static int countByUsersId(int $userId)
 * @method int countSubscribers($parameters = null)
 * @method Simple getReplies($parameters = null)
 * @method Simple getViews($parameters = null)
 * @method Simple getPollOptions($parameters = null)
 * @method Simple getPollVotes($parameters = null)
 *
 * @package Phosphorum\Model
 */
class Posts extends Model
{
    const IS_DELETED = 1;
    const IS_STICKED = 'Y';
    const IS_UNSTICKED = 'N';

    public $id;

    public $users_id;

    public $categories_id;

    public $title;

    public $slug;

    public $content;

    public $number_views;

    public $number_replies;

    public $votes_up;

    public $votes_down;

    public $sticked;

    public $modified_at;

    public $created_at;

    public $edited_at;

    public $status;

    public $locked;

    public $deleted;

    public $accepted_answer;

    public function initialize()
    {
        $this->belongsTo(
            'users_id',
            'Phosphorum\Model\Users',
            'id',
            [
                'alias'    => 'user',
                'reusable' => true
            ]
        );

        $this->belongsTo(
            'categories_id',
            'Phosphorum\Model\Categories',
            'id',
            [
                'alias'      => 'category',
                'reusable'   => true,
                'foreignKey' => [
                    'message' => 'The category is not valid'
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Phosphorum\Model\PostsPollOptions',
            'posts_id',
            [
                'alias' => 'pollOptions'
            ]
        );

        $this->hasMany(
            'id',
            'Phosphorum\Model\PostsPollVotes',
            'posts_id',
            [
                'alias' => 'pollVotes'
            ]
        );

        $this->hasMany(
            'id',
            'Phosphorum\Model\PostsReplies',
            'posts_id',
            [
                'alias' => 'replies'
            ]
        );

        $this->hasMany(
            'id',
            'Phosphorum\Model\PostsViews',
            'posts_id',
            [
                'alias' => 'views'
            ]
        );

        $this->hasMany(
            'id',
            'Phosphorum\Model\PostsSubscribers',
            'posts_id',
            [
                'alias' => 'subscribers'
            ]
        );

        $this->addBehavior(
            new SoftDelete(
                [
                    'field' => 'deleted',
                    'value' => self::IS_DELETED
                ]
            )
        );

        $this->addBehavior(
            new Timestampable(
                [
                    'beforeCreate' => [
                        'field' => ['created_at', 'modified_at'],
                    ]
                ]
            )
        );
    }

    public function beforeValidationOnCreate()
    {
        $this->deleted         = 0;
        $this->number_views    = 0;
        $this->number_replies  = 0;
        $this->sticked         = self::IS_UNSTICKED;
        $this->accepted_answer = 'N';
        $this->locked          = 'N';
        $this->status          = 'A';

        if ($this->title && !$this->slug) {
            $this->slug = $this->getDI()->getShared('slug')->generate($this->title);
        }
    }

    /**
     * Create a posts-views logging the ipaddress where the post was created
     * This avoids that the same session counts as post view
     */
    public function beforeCreate()
    {
        $this->views = new PostsViews([
            'ipaddress' => $this->getDI()->getShared('request')->getClientAddress(),
        ]);
    }

    public function afterCreate()
    {
        /**
         * Register a new activity
         */
        if ($this->id > 0) {
            /**
             * Register the activity
             */
            $activity           = new Activities();
            $activity->users_id = $this->users_id;
            $activity->posts_id = $this->id;
            $activity->type     = Activities::NEW_POST;
            $activity->save();

            /**
             * Notify users that always want notifications
             */
            $notification           = new PostsNotifications();
            $notification->users_id = $this->users_id;
            $notification->posts_id = $this->id;
            $notification->save();

            /**
             * Notify users that always want notifications
             */
            $toNotify = [];
            foreach (Users::find(['notifications = "Y"', 'columns' => 'id']) as $user) {
                if ($this->users_id != $user->id) {
                    $notification           = new Notifications();
                    $notification->users_id = $user->id;
                    $notification->posts_id = $this->id;
                    $notification->type     = Notifications::TYPE_POST;
                    $notification->save();
                    $toNotify[$user->id] = $notification->id;
                }
            }

            /**
             * Update the total of posts related to a category
             */
            $this->category->number_posts++;
            $this->category->save();

            /**
             * Queue notifications to be sent
             */
            $this->getDI()->getShared('queue')->put($toNotify);
        }
    }

    public function afterSave()
    {
        $this->clearCache();

        // In case of updating post through creating PostsViews
        if (!$this->getDI()->getShared('session')->has('identity')) {
            return;
        }

        $history = new PostsHistory([
            'posts_id' => $this->id,
            'users_id' => $this->getDI()->getShared('session')->get('identity'),
            'content'  => $this->content,
        ]);

        if (!$history->save()) {
            /** @var \Phalcon\Logger\AdapterInterface $logger */
            $logger   = $this->getDI()->get('logger');
            $messages = $history->getMessages();
            $reason   = [];

            foreach ($messages as $message) {
                /** @var \Phalcon\Mvc\Model\MessageInterface $message */
                $reason[] = $message->getMessage();
            }

            $logger->error('Unable to store post history. Post id: {id}. Reason: {reason}', [
                'id'     => $this->id,
                'reason' => implode('. ', $reason)
            ]);
        }
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * Returns a W3C date to be used in the sitemap.
     *
     * @return string
     */
    public function getUTCModifiedAt()
    {
        $modifiedAt = new DateTime('@' . $this->modified_at, new DateTimeZone('UTC'));

        return $modifiedAt->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * @return array
     */
    public function getRecentUsers()
    {
        $users  = [$this->user->id => [$this->user->login, $this->user->email]];
        foreach ($this->getReplies(['order' => 'created_at DESC', 'limit' => 3]) as $reply) {
            if (!isset($users[$reply->user->id])) {
                $users[$reply->user->id] = [$reply->user->login, $reply->user->email];
            }
        }
        return $users;
    }

    /**
     * @return string
     */
    public function getHumanNumberViews()
    {
        $number = $this->number_views;
        if ($number > 1000) {
            return round($number / 1000, 1) . 'k';
        } else {
            return $number;
        }
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

    /**
     * @return bool|string
     */
    public function getHumanModifiedAt()
    {
        if ($this->modified_at != $this->created_at) {
            $diff = time() - $this->modified_at;
            if ($diff > (86400 * 30)) {
                return date('M \'y', $this->modified_at);
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

        return false;
    }

    /**
     * Checks if the post can have a bounty
     *
     * @return boolean
     */
    public function canHaveBounty()
    {
        $canHave = $this->accepted_answer != 'Y'
            && $this->sticked != self::IS_STICKED
            && $this->number_replies == 0
            && $this->categories_id != 15
            && // announcements
            $this->categories_id != 1
            && // no_bounty
            $this->category->no_bounty != 'Y'
            && // offtopic
            $this->categories_id != 7
            && //jobs
            $this->categories_id != 24
            && //show community
            ($this->votes_up - $this->votes_down) >= 0;

        if ($canHave) {
            $diff = time() - $this->created_at;
            if ($diff > 86400 && $diff < (86400 * 30)) {
                return true;
            } elseif ($diff < 3600) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculates a bounty for the post
     *
     * @return array|bool
     */
    public function getBounty()
    {
        $diff = time() - $this->created_at;
        if ($diff > 86400) {
            if ($diff < (86400 * 30)) {
                return ['type' => 'old', 'value' => 150 + intval($diff / 86400 * 3)];
            }
        } elseif ($diff < 3600) {
            return ['type' => 'fast-reply', 'value' => 100];
        }

        return false;
    }

    /**
     * Checks if the Post has replies
     *
     * @return bool
     */
    public function hasReplies()
    {
        return $this->number_replies > 0;
    }

    /**
     * Checks if the Post has accepted answer
     *
     * @return bool
     */
    public function hasAcceptedAnswer()
    {
        return 'Y' == $this->accepted_answer;
    }

    /**
     * Checks if the Post has a Poll
     *
     * @return bool
     */
    public function hasPoll()
    {
        return $this->getPollOptions()->valid();
    }

    /**
     * Checks if User is participated in a Poll
     *
     * @param int $userId User ID
     * @return bool
     */
    public function isParticipatedInPoll($userId)
    {
        if (!$userId) {
            return false;
        }

        return $this->getPollVotes(['users_id = :id:', 'bind' => ['id' => $userId]])->valid();
    }

    /**
     * Checks if the voting for the poll was started
     *
     * @return bool
     */
    public function isStartVoting()
    {
        return $this->getPollVotes()->count() > 0;
    }

    /**
     * Checks whether a specific user is subscribed to the post
     *
     * @param int $userId
     * @return bool
     */
    public function isSubscribed($userId)
    {
        return $this->countSubscribers(['users_id = :userId:', 'bind' => ['userId' => $userId]]) > 0;
    }

    /**
     * Clears the cache related to this post
     */
    public function clearCache()
    {
        if ($this->id) {
            $viewCache = $this->getDI()->getShared('viewCache');
            $viewCache->delete('post-' . $this->id);
            $viewCache->delete('post-body-' . $this->id);
            $viewCache->delete('post-users-' . $this->id);
            $viewCache->delete('sidebar');
        }
    }

    public function getDifference()
    {
        $history = PostsHistory::findLast($this);

        if (!$history->valid()) {
            return false;
        }

        if ($history->count() > 1) {
            $history = $history->offsetGet(1);
        } else {
            $history = $history->getFirst();
        }

        /** @var PostsHistory $history */

        $b = explode("\n", $history->content);

        $diff = new Diff($b, explode("\n", $this->content), []);
        $difference = $diff->render(new SideBySide);

        return $difference;
    }
}
