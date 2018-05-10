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

namespace Phosphorum\Provider\Queue;

/**
 * Phosphorum\Provider\Queue\Fake
 *
 * @package Phosphorum\Provider\Queue
 */
class Fake
{
    protected $queue;

    /**
     * Server constructor.
     *
     * @param mixed $queue
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    /**
     * Simulates putting a job in the queue.
     *
     * @param  array $params
     * @return bool
     */
    public function sendMessage(array $params)
    {
        container('logger')->debug('Putting job: ' . $params['MessageBody']);

        return true;
    }

    /**
     * Simulates getting queue url
     * @param array $params
     * @return array
     */
    public function getQueueUrl(array $params)
    {
        return $this;
    }

    /**
     * Simulates getting param from `Aws\Result`
     * @param string $queueParam
     * @return bool
     */
    public function get($queueParam)
    {
        return true;
    }

    public function deleteMessage(array $param)
    {
        return true;
    }
}
