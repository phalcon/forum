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

namespace Phosphorum\Discord;

use Phalcon\Di;
use Phalcon\Di\Injectable;
use Phosphorum\Model\Posts;
use Aws\Exception\AwsException;
use Phosphorum\Model\PostsReplies;
use Phosphorum\Services\QueueService;
use Phosphorum\Model\Services\Service\Posts as PostsService;

/**
 * Phosphorum\Discord\DiscordComponent
 *
 * @property \Aws\AwsClientInterface $queue
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

            $this->addToQueue(
                [
                    'message' => $message,
                    'embed' => $embed,
                ],
                'discussion'
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
                ":newspaper2: **`%s` has commented in discussion `%s`. Check it out on %s#C%s.**",
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
            $this->addToQueue(
                [
                    'message' => $message,
                    'embed' => $embed,
                ],
                'reply'
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
                ":newspaper2: **Discussion `%s` was marked as solved. Check out accepted answer on %s#C%s.**",
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
            $this->addToQueue(
                [
                    'message' => $message,
                    'embed' => $embed,
                ],
                'solved discusiion'
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

    /**
     * Add information array to discord queue.
     * @param array $data
     * @param string $title
     * @return void
     */
    protected function addToQueue(array $data, $title = '')
    {
        if (empty($data)) {
            return;
        }

        $queueName = (new QueueService())->getFullQueueName('discord');
        try {
            $queue = Di::getDefault()->get('queue');
            $queue->sendMessage([
                'DelaySeconds' => 1,
                'MessageAttributes' => [
                    "Title" => [
                        'DataType' => "String",
                        'StringValue' => "Message about " . $title,
                    ],
                ],
                'MessageBody' => json_encode($data),
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
}
