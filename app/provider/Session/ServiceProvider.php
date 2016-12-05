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

namespace Phosphorum\Provider\Session;

use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Session\ServiceProvider
 *
 * @package Phosphorum\Provider\Session
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'session';

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
                $config = container('config')->session;

                $driver   = $config->drivers->{$config->default};
                $adapter  = '\Phalcon\Session\Adapter\\' . $driver->adapter;
                $defaults = [
                    'prefix'   => $config->prefix,
                    'uniqueId' => $config->uniqueId,
                    'lifetime' => $config->lifetime,
                ];

                /** @var \Phalcon\Session\AdapterInterface $session */
                $session = new $adapter(array_merge($driver->toArray(), $defaults));
                $session->start();

                return $session;
            }
        );
    }
}
