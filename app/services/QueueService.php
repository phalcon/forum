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

namespace Phosphorum\Services;

use Phalcon\Di;

/**
 * Service provides suit to work with queue
 * Phosphorum\Services\QueueService
 *
 * @package Phosphorum\Services
 */
class QueueService
{
    /**
     * <code>
     * Create queue name like `development-phalcon-forum-notifications`.
     * `development` - `APP_ENV` option from `.env`
     * `phalcon` - `PRODUCT` option from `.env`
     * `forum` - static string
     * `notifications` - method param
     *
     * $queueService = new QueueService();
     *
     * If `PRODUCT=phalcon` in `.env`
     * echo $queueService->getFullQueueName('notification');
     * `development-phalcon-forum-notifications`
     *
     * If `.env` doen't have `PRODUCT` option
     * echo $queueService->getFullQueueName('notification');
     * `forum-notifications`
     * </code>
     *
     * @param string $queue
     * @return string
     */
    public function getFullQueueName($queue)
    {
        return $this->getQueueFromEnv() . 'forum' . '-' . $queue;
    }

    /**
     * @return string
     */
    private function getQueueFromEnv()
    {
        if (getenv('PRODUCT')) {
            return  getenv('APP_ENV') . "-" . getenv('PRODUCT') . '-';
        }

        return '';
    }
}
