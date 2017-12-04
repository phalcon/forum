<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
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

use Phosphorum\Mail\SendSpool;
use Phosphorum\Console\AbstractTask;

/**
 * Phosphorum\Task\Notifications
 *
 * @package Phosphorum\Task
 */
class Notifications extends AbstractTask
{
    /**
     * @Doc("Check notifications marked as not send on the databases and send them")
     */
    public function send()
    {
        $spool = new SendSpool();

        try {
            $spool->sendRemaining();
        } catch (\Exception $t) {
            $message = '[{class}]: Failed to send totification: {message} on {file}:{line}';
            container('logger')->error($message, [
                'class'   => get_class($t),
                'message' => $t->getMessage(),
                'file'    => $t->getFile(),
                'line'    => $t->getLine(),
            ]);
        } catch (\Throwable $e) {
            $message = '[{class}]: Failed to send totification: {message} on {file}:{line}';
            container('logger')->error($message, [
                'class'   => get_class($e),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }

    /**
     * @Doc("Check the queue and send the notifications scheduled there")
     */
    public function queue()
    {
        $spool = new SendSpool();

        try {
            $spool->consumeQueue();
        } catch (\Exception $t) {
            $message = '[{class}]: Failed to send totification: {message} on {file}:{line}';
            container('logger')->error($message, [
                'class'   => get_class($t),
                'message' => $t->getMessage(),
                'file'    => $t->getFile(),
                'line'    => $t->getLine(),
            ]);
        } catch (\Throwable $e) {
            $message = '[{class}]: Failed to send totification: {message} on {file}:{line}';
            container('logger')->error($message, [
                'class'   => get_class($e),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }
}
