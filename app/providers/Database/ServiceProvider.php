<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Providers\Database;

use PDO;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Db\AdapterInterface;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\Database\ServiceProvider
 *
 * @package Phosphorum\Providers\Database
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'db';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config')->get('database');
                $em     = container('eventsManager');

                $driver  = $config->drivers->{$config->default};
                $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $driver->adapter;

                $config = $driver->toArray();
                unset($config['adapter']);

                /** @var \Phalcon\Db\Adapter\Pdo $connection */
                $connection = new $adapter($config);

                // Listen all the database events
                $em->attach(
                    'db',
                    function ($event, $connection) {
                        /**
                         * @var Event $event
                         * @var AdapterInterface $connection
                         */
                        if ($event->getType() == 'beforeQuery') {
                            $variables = $connection->getSQLVariables();
                            $string    = $connection->getSQLStatement();

                            if ($variables) {
                                $string .= ' [' . join(',', $variables) . ']';
                            }

                            // To disable logging change logLevel in config
                            container()->get('logger', ['db'])->debug($string);
                        }
                    }
                );

                $connection->setEventsManager($em);

                return $connection;
            }
        );
    }
}
