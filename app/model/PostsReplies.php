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
use Phalcon\Queue\Beanstalk;
use Phosphorum\Discord\DiscordComponent;
use Phalcon\Diff\Renderer\Html\SideBySide;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Events\Manager as EventsManager;
use Phosphorum\Listener\PostRepliesListener;

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

        $this->keepSnapshots(true);

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

        $eventsManager = new EventsManager();
        $eventsManager->attach('model', new PostRepliesListener());
        $this->setEventsManager($eventsManager);
    }

    /**
     * @return bool|string
     */
    public function getHumanCreatedAt()
    {
        $diff = time() - $this->created_at;
        if ($diff > (86400 * 30)) {
            return date('M \'y', $this->created_at);
        }

        $this->getDiffTime($diff);
    }

    /**
     * @return bool|string
     */
    public function getHumanEditedAt()
    {
        $diff = time() - $this->edited_at;
        if ($diff > (86400 * 30)) {
            return date('M \'y', $this->edited_at);
        }

        $this->getDiffTime($diff);
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

    /**
     * Returns a W3C date to be used in the sitemap.
     *
     * @return string
     */
    public function getUTCCreatedAt()
    {
        $modifiedAt = new DateTime('@' . $this->created_at, new DateTimeZone('UTC'));

        return $modifiedAt->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Return different time
     *
     * @param int $diff
     * @return string
     */
    protected function getDiffTime($diff)
    {
        if ($diff > 86400) {
            return ((int)($diff / 86400)) . 'd ago';
        }

        if ($diff > 3600) {
            return ((int)($diff / 3600)) . 'h ago';
        }

        return ((int)($diff / 60)) . 'm ago';
    }
}
