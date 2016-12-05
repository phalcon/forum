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

namespace Phosphorum\Provider\Database;

use Phosphorum\Listener\Database;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Database\ServiceProvider
 *
 * @package Phosphorum\Provider\Database
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'db';

    /**
     * {@inheritdoc}
     * Database connection is created based in the parameters defined in the configuration file.
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config')->database;
                $em     = container('eventsManager');

                $driver  = $config->drivers->{$config->default};
                $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $driver->adapter;

                $config = $driver->toArray();
                unset($config['adapter']);

                /** @var \Phalcon\Db\Adapter\Pdo $connection */
                $connection = new $adapter($config);

                $em->attach('db', new Database());

                $connection->setEventsManager($em);

                return $connection;
            }
        );
    }
}
