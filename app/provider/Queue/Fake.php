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
     * @param  array $job
     * @return bool
     */
    public function put(array $job)
    {
        singleton('logger')->debug('Putting job: ' . json_encode($job));

        return true;
    }

    /**
     * Simulates retrieving messages.
     *
     * @return bool
     */
    public function peekReady()
    {
        return false;
    }
}
