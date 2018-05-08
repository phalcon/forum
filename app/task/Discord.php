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

namespace Phosphorum\Task;

use Aws\Sqs\SqsClient;
use League\CLImate\CLImate;
use Discord\Parts\Channel\Channel;
use Phosphorum\Console\AbstractTask;
use Phosphorum\Discord\DiscordComponent;
use Phosphorum\Exception\InvalidParameterException;

/**
 * Phosphorum\Task\Discord
 *
 * @package Phosphorum\Task
 */
class Discord extends AbstractTask
{
    /**
     * @Doc("Send notifications to the Discord")
     */
    public function send()
    {
        $queue = $this->getQueue();

        /** @var DiscordComponent $discordService */
        $discordService = container('discord');

        $discord = new \Discord\Discord(
            [
                'token' => $discordService->getToken(),
            ]
        );

        $discord->loop->addPeriodicTimer(
            5,
            function () use ($queue, $discord, $discordService) {
                $guild = $discord->guilds->get('id', $discordService->getGuildId());

                if (!is_object($guild)) {
                    throw new \RuntimeException("Looks like you didn't add bot to your guild.");
                }

                /**@var Channel $channel */
                $channel = $guild->channels->get('id', $discordService->getChannelId());

                $this->tryToSendMessagesFromQueue($queue, $channel);
            }
        ); // each 5 seconds get jobs from queue and send to channel

        $discord->on(
            'ready',
            function () {
                // we don't listen anything so we don't need it
            }
        );

        $discord->run();
    }

    /**
     * Get queue service provider
     * @return SqsClient
     * @throws InvalidParameterException
     */
    protected function getQueue()
    {
        $queue = container('queue');
        if ($queue instanceof SqsClient) {
            return $queue;
        }

        (new CLImate)->error('This task does not works with Fake queue adapter.' . PHP_EOL . 'Exit...');
        throw new InvalidParameterException();
    }

    /**
     * Get messages from queue, max amount 10
     * @param SqsClient $queue
     * @return \Aws\Result
     */
    protected function getMessagesFromQueue(SqsClient $queue)
    {
        return $queue->receiveMessage([
            'AttributeNames' => ['SentTimestamp'],
            'MaxNumberOfMessages' => 10,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $queue->getQueueUrl(['QueueName' => 'discord'])->get('QueueUrl'),
            'WaitTimeSeconds' => 0,
        ]);
    }

    /**
     * @param SqsClient $queue
     * @param Channel $channel
     */
    protected function tryToSendMessagesFromQueue(SqsClient $queue, Channel $channel)
    {
        $messages = $this->getMessagesFromQueue($queue);
        if (count($messages->get('Messages')) == 0) {
            return;
        }

        foreach ($messages->get('Messages') as $message) {
            $body = json_decode($message['Body'], true);
            if (empty($body['message']) || empty($body['embed'])) {
                container('logger', ['discord'])->error('Looks like response is broken. Message: {message}', [
                    'message' => json_encode($message)
                ]);
            } else {
                $channel->sendMessage($body['message'], false, $body['embed']);
            }

            $queue->deleteMessage([
                'QueueUrl' => $queue->getQueueUrl(['QueueName' => 'discord'])->get('QueueUrl'),
                'ReceiptHandle' => $message['ReceiptHandle'],
            ]);
        }

        $this->tryToSendMessagesFromQueue($queue, $channel);
    }
}
