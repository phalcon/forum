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

use Phosphorum\Mail\SendNotifications;
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
        // Exit to avoid  race condition
        $filehandle = fopen(storage_path('pids/notifications-send.lock'), 'c+');

        if (flock($filehandle, LOCK_EX | LOCK_NB)) {
            try {
                $spool = new SendNotifications();
                $spool->sendRemaining();
            } catch (\Exception $t) {
                $message = '[{class}]: Failed to send notification: {message} on {file}:{line}';
                container('logger')->error($message, [
                    'class'   => get_class($t),
                    'message' => $t->getMessage(),
                    'file'    => $t->getFile(),
                    'line'    => $t->getLine(),
                ]);
            } catch (\Throwable $e) {
                $message = '[{class}]: Failed to send notification: {message} on {file}:{line}';
                container('logger')->error($message, [
                    'class'   => get_class($e),
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);
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

        if (file_exists(storage_path('pids/notifications-send.lock'))) {
            @unlink(storage_path('pids/notifications-send.lock'));
        }
    }

    /**
     * @Doc("Check the queue and send the notifications scheduled there")
     */
    public function queue()
    {
        // Exit to avoid  race condition
        $filehandle = fopen(storage_path('pids/notifications-queue.lock'), 'c+');

        if (flock($filehandle, LOCK_EX | LOCK_NB)) {
            try {
                $spool = new SendNotifications();
                $spool->consumeQueue();
            } catch (\Exception $t) {
                $message = '[{class}]: Failed to send notification: {message} on {file}:{line}';
                container('logger')->error($message, [
                    'class'   => get_class($t),
                    'message' => $t->getMessage(),
                    'file'    => $t->getFile(),
                    'line'    => $t->getLine(),
                ]);
            } catch (\Throwable $e) {
                $message = '[{class}]: Failed to send notification: {message} on {file}:{line}';
                container('logger')->error($message, [
                    'class'   => get_class($e),
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);
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
}
