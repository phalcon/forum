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

namespace Phosphorum\Queue;

/**
 * DummyServer
 *
 * This classs replaces Beanstalkd by a dummy server
 */
class DummyServer
{
    /**
     * Simulates putting a job in the queue
     *
     * @param array $job
     */
    public function put($job)
    {
        return true;
    }
}
