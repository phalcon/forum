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
use Aws\AwsClientInterface;
use RestCord\DiscordClient;
use Phosphorum\Console\AbstractTask;
use Phosphorum\Services\QueueService;
use Phosphorum\Exception\InvalidParameterException;

/**
 * Phosphorum\Task\Discord
 *
 * @package Phosphorum\Task
 */
class Discord extends AbstractTask
{
    /**@var AwsClientInterface | SqsClient $queue */
    private $queue;

    /**@var DiscordClient $discord */
    private $discord;

    /**@var QueueService $queueService */
    private $queueService;

    /**
     * @Doc("Send notifications to the Discord")
     */
    public function send()
    {
        $this->init();
        $this->tryToSendMessagesFromQueue();
    }

    /**
     * @return void
     */
    private function init()
    {
        $this->queue = $this->getQueue();
        $this->discord = new DiscordClient(['token' => container('discord')->getToken()]);
        $this->queueService = new QueueService();
    }

    /**
     * Get queue service provider
     * @return SqsClient
     * @throws InvalidParameterException
     */
    private function getQueue()
    {
        $queue = container('queue');
        if ($queue instanceof AwsClientInterface) {
            return $queue;
        }

        $this->outputError('This task does not works with Fake queue adapter.' . PHP_EOL . 'Exit...');
        throw new InvalidParameterException();
    }

    /**
     * @return void
     */
    private function tryToSendMessagesFromQueue()
    {
        $messages = $this->getMessagesFromQueue($this->queue);
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
                $this->discord->channel->createMessage([
                    'channel.id' => (int)container('discord')->getChannelId(),
                    'content' => $body['message'],
                    'embed' => $body['embed'],
                ]);
            }

            $this->queue->deleteMessage([
                'QueueUrl' => $this->queue->getQueueUrl([
                    'QueueName' => $this->queueService->getFullQueueName('discord')])->get('QueueUrl'),
                'ReceiptHandle' => $message['ReceiptHandle'],
            ]);
        }

        $this->tryToSendMessagesFromQueue();
    }

    /**
     * Get messages from queue, max amount 10
     * @param AwsClientInterface | SqsClient $queue
     * @return \Aws\Result
     */
    private function getMessagesFromQueue(AwsClientInterface $queue)
    {
        return $queue->receiveMessage([
            'AttributeNames' => ['SentTimestamp'],
            'MaxNumberOfMessages' => 10,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $queue->getQueueUrl([
                'QueueName' => $this->queueService->getFullQueueName('discord')
            ])->get('QueueUrl'),
            'WaitTimeSeconds' => 0,
        ]);
    }
}
