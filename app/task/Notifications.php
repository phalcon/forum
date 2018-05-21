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

use Phosphorum\Console\AbstractTask;
use Phosphorum\Mail\SendNotifications;

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
        $this->handleTask('sendRemaining');
    }

    /**
     * @Doc("Check the queue and send the notifications scheduled there")
     */
    public function queue()
    {
        $this->handleTask('consumeQueue');
    }

    /**
     * @return void
     */
    protected function handleTask($funcName)
    {
        // Exit to avoid  race condition
        $filehandle = fopen(storage_path('pids/notifications-queue.lock'), 'c+');

        if (flock($filehandle, LOCK_EX | LOCK_NB)) {
            try {
                (new SendNotifications())->$funcName();
            } catch (\Exception $t) {
                $this->addErrorMessageToLog($t);
            } catch (\Throwable $e) {
                $this->addErrorMessageToLog($e);
            }

            // don't forget to release the lock
            flock($filehandle, LOCK_UN);
        } else {
            container('logger')->warning('The {task} already running. Exit...', [
                'task'    => __METHOD__,
            ]);
        }

        if (is_resource($filehandle)) {
            fclose($filehandle);
        }

        if (file_exists(storage_path('pids/notifications-queue.lock'))) {
            @unlink(storage_path('pids/notifications-queue.lock'));
        }
    }

    /**
     * @return void
     */
    protected function addErrorMessageToLog(\Throwable $t)
    {
        $message = '[{class}]: Failed to send notification: {message} on {file}:{line}';
        container('logger')->error($message, [
            'class'   => get_class($t),
            'message' => $t->getMessage(),
            'file'    => $t->getFile(),
            'line'    => $t->getLine(),
        ]);
    }
}
