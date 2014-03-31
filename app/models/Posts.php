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

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;

/**
 * Class Posts
 *
 * @property \Phosphorum\Models\Users          user
 * @property \Phosphorum\Models\Categories     category
 * @property \Phosphorum\Models\PostsReplies[] replies
 * @property \Phosphorum\Models\PostsViews[]   views
 *
 * @method static Posts findFirstById
 * @method static Posts findFirst($parameters = null)
 * @method static Posts[] find($parameters = null)
 * @method PostsReplies[] getReplies
 *
 * @package Phosphorum\Models
 */
class Posts extends Model
{

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

    public $deleted;

    public $accepted_answer;

    public function initialize()
    {
        $this->belongsTo(
            'users_id',
            'Phosphorum\Models\Users',
            'id',
            array(
             'alias'    => 'user',
                'reusable' => true
            )
        );

        $this->belongsTo(
            'categories_id',
            'Phosphorum\Models\Categories',
            'id',
            array(
                'alias'      => 'category',
                'reusable'   => true,
                'foreignKey' => array(
                    'message' => 'The category is not valid'
                )
            )
        );

        $this->hasMany(
            'id',
            'Phosphorum\Models\PostsReplies',
            'posts_id',
            array(
                'alias' => 'replies'
            )
        );

        $this->hasMany(
            'id',
            'Phosphorum\Models\PostsViews',
            'posts_id',
            array(
                'alias' => 'views'
            )
        );

    }

    public function beforeValidationOnCreate()
    {
        $this->deleted         = 0;
        $this->number_views    = 0;
        $this->number_replies  = 0;
        $this->sticked         = 'N';
        $this->accepted_answer = 'N';
        $this->status          = 'A';
    }

    /**
     * Create a posts-views logging the ipaddress where the post was created
     * This avoids that the same session counts as post view
     */
    public function beforeCreate()
    {
        $postView            = new PostsViews();
        $postView->ipaddress = $this->getDI()->getRequest()->getClientAddress();
        $this->views         = $postView;

        $this->created_at    = time();
        $this->modified_at   = time();
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
            $activity->type     = 'P';
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
            $toNotify = array();
            foreach (Users::find(array('notifications = "Y"', 'columns' => 'id')) as $user) {
                if ($this->users_id != $user->id) {
                    $notification           = new Notifications();
                    $notification->users_id = $user->id;
                    $notification->posts_id = $this->id;
                    $notification->type     = 'P';
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
            $this->getDI()->getQueue()->put($toNotify);
        }
    }

    public function afterSave()
    {

        $this->clearCache();

        $history           = new PostsHistory();
        $history->posts_id = $this->id;
        $history->users_id = $this->getDI()->getSession()->get('identity');
        $history->content  = $this->content;
        $history->save();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * Returns a W3C date to be used in the sitemap
     *
     * @return string
     */
    public function getUTCModifiedAt()
    {
        $modifiedAt = new \DateTime();
        $modifiedAt->setTimezone(new \DateTimeZone('UTC'));
        $modifiedAt->setTimestamp($this->modified_at);
        return $modifiedAt->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * @return array
     */
    public function getRecentUsers()
    {

        $users  = array($this->user->id => array($this->user->login, $this->user->gravatar_id));
        foreach ($this->getReplies(['order' => 'created_at DESC', 'limit' => 3]) as $reply) {
            if (!isset($users[$reply->user->id])) {
                $users[$reply->user->id] = array($reply->user->login, $reply->user->gravatar_id);
            }
        }
        return $users;
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
    }

    /**
     * Checks if the post can have a bounty
     *
     * @return boolean
     */
    public function canHaveBounty()
    {
        $canHave = $this->accepted_answer != "Y"
            && $this->sticked != 'Y'
            && $this->number_replies == 0
            && $this->categories_id != 15
            && //announcements
            $this->categories_id != 4
            && //offtopic
            $this->categories_id != 7
            && //jobs
            $this->categories_id != 24
            && //show community
            ($this->votes_up - $this->votes_down) >= 0;
        if ($canHave) {
            $diff = time() - $this->created_at;
            if ($diff > 86400) {
                if ($diff < (86400 * 30)) {
                    return true;
                }
            } else {
                if ($diff < 3600) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getBounty()
    {
        $diff = time() - $this->created_at;
        if ($diff > 86400) {
            if ($diff < (86400 * 30)) {
                return array('type' => 'old', 'value' => 150 + intval($diff / 86400 * 3));
            }
        } else {
            if ($diff < 3600) {
                return array('type' => 'fast-reply', 'value' => 100);
            }
        }
        return false;
    }

    public function clearCache()
    {
        if ($this->id) {
            $viewCache = $this->getDI()->getViewCache();
            $viewCache->delete('post-' . $this->id);
            $viewCache->delete('post-body-' . $this->id);
            $viewCache->delete('post-users-' . $this->id);
            $viewCache->delete('sidebar');
        }
    }
}
