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

namespace Phosphorum\Listener;

use Phalcon\Events\Event;
use Phalcon\Db\Adapter\Pdo;

/**
 * Phosphorum\Listener\Database
 *
 * @package Phosphorum\Listener
 */
class Database
{
    /**
     * Database queries listener.
     *
     * You can disable queries logging by changing log level.
     *
     * @param  Event $event
     * @param  Pdo   $connection
     * @return bool
     */
    public function beforeQuery(Event $event, Pdo $connection)
    {
        $string    = $connection->getSQLStatement();
        $variables = $connection->getSqlVariables();
        $context   = $variables ?: [];

        $logger = container()->get('logger', ['db']);

        if (!empty($context)) {
            $context = ' [' . implode(', ', $context) . ']';
        } else {
            $context = '';
        }

        $logger->debug($string . $context);

        return true;
    }
}
