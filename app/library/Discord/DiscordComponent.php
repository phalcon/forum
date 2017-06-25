<?php

namespace Phosphorum\Discord;

use Exception;
use Phalcon\Di\Injectable;
use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Http\Client\Request;
use Phosphorum\Model\Posts;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Model\Services\Service\Posts as PostsService;

/**
 * Class DiscordComponent
 *
 * @property \Phalcon\Queue\Beanstalk $queue
 * @package Phosphorum\Discord
 */
class DiscordComponent extends Injectable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Creates component
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->options['token_bot'];
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->options['channel_id'];
    }

    /**
     * @return mixed
     */
    public function getGuildId()
    {
        return $this->options['guild_id'];
    }

    /**
     * @return bool
     */
    public function isMessagingAboutNewDiscussion()
    {
        return $this->options['message']['new_discussions'] == true;
    }

    /**
     * @return bool
     */
    public function isMessagingAboutReplies()
    {
        return $this->options['message']['new_replies'] == true;
    }

    /**
     * @return bool
     */
    public function isMessagingAboutSolvedDiscussion()
    {
        return $this->options['message']['solved_discussions'] == true;
    }

    /**
     * @param Posts $posts
     */
    public function addMessageAboutDiscussion(Posts $posts)
    {
        if ($this->isMessagingAboutNewDiscussion()) {
            /** @var PostsService $postService */
            $postService = container(PostsService::class);

            $href = $postService->getPostUrl($posts);
            $message = sprintf(
                ":newspaper2: **New discussion `%s` was started by `%s`. Check it out on %s.**",
                $posts->title,
                $posts->user->name,
                $href
            );
            $embed = [
                'title' => $posts->title,
                'description' => $this->makeDescription($posts->content, 375),
                'url' => $href,
                'timestamp' => $posts->getUTCModifiedAt(),
                'type' => 'rich',
                'author' => [
                    'name' => $posts->user->name,
                    'url' => container('config')->site->url . "/user/{$posts->users_id}/{$posts->user->login}",
                    'icon_url' => $this->gravatar->setSize(220)->getAvatar($posts->user->email),
                ],
            ];
            $this->queue->choose('discord');
            $this->queue->put(
                [
                    'message' => $message,
                    'embed' => $embed,
                ]
            );
        }
    }

    /**
     * @param PostsReplies $reply
     */
    public function addMessageAboutReply(PostsReplies $reply)
    {
        if ($this->isMessagingAboutReplies()) {
            $postService = container(PostsService::class);

            $href = $postService->getPostUrl($reply->post);
            $message = sprintf(
                "\":newspaper2: **`%s` has commented in discussion `%s`.  Check it out on %s#C%s.**",
                $reply->user->name,
                $reply->post->title,
                $href,
                $reply->id
            );
            $embed = [
                'title' => $reply->post->title,
                'description' => $this->makeDescription($reply->content, 375),
                'url' => "$href#C{$reply->id}",
                'timestamp' => $reply->getUTCCreatedAt(),
                'type' => 'rich',
                'author' => [
                    'name' => $reply->user->name,
                    'url' => container('config')->site->url . "/user/{$reply->users_id}/{$reply->user->login}",
                    'icon_url' => $this->gravatar->setSize(220)->getAvatar($reply->user->email),
                ],
            ];
            $this->queue->choose('discord');
            $this->queue->put(
                [
                    'message' => $message,
                    'embed' => $embed,
                ]
            );
        }
    }

    /**
     * @param PostsReplies $reply
     */
    public function addMessageAboutSolvedDiscussion(PostsReplies $reply)
    {
        if ($this->isMessagingAboutSolvedDiscussion()) {
            $postService = container(PostsService::class);

            $href = $postService->getPostUrl($reply->post);
            $message = sprintf(
                ":newspaper2: **Discussion `%s`  was marked as solved. Check out accepted answer on %s#C%s.**",
                $reply->post->title,
                $href,
                $reply->id
            );
            $embed = [
                'title' => $reply->post->title,
                'description' => $this->makeDescription($reply->content, 375),
                'url' => "$href#C{$reply->id}",
                'timestamp' => $reply->getUTCCreatedAt(),
                'type' => 'rich',
                'author' => [
                    'name' => $reply->user->name,
                    'url' => container('config')->site->url . "/user/{$reply->users_id}/{$reply->user->login}",
                    'icon_url' => $this->gravatar->setSize(220)->getAvatar($reply->user->email),
                ],
            ];
            $this->queue->choose('discord');
            $this->queue->put(
                [
                    'message' => $message,
                    'embed' => $embed,
                ]
            );
        }
    }

    /**
     * @param $description
     * @param $toLength
     * @return mixed|string
     */
    protected function makeDescription($description, $toLength)
    {
        $description = str_replace(["\n", "\r", "\t"], ' ', $description);
        $description = trim($description);
        $padding = substr($description, $toLength);
        if ($padding === 0) {
            return $description;
        }
        $lengthDot = strpos($padding, ".");
        if ($lengthDot === 0) {
            return $description;
        }
        $lengthSpace = strpos($padding, " ");
        if ($lengthSpace === 0) {
            return $description;
        }

        return substr($description, 0, min($lengthDot + $toLength + 1, $lengthSpace + $toLength)) . "...";
    }
}
