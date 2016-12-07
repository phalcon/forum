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

namespace Phosphorum\Provider\ViewCache;

use Phalcon\Cache\Frontend\Output;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\ViewCache\ServiceProvider
 *
 * @package Phosphorum\Provider\ViewCache
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'viewCache';

    /**
     * {@inheritdoc}
     *
     * Note: The frontend must always be Phalcon\Cache\Frontend\Output and the
     * service 'viewCache' must be registered as always open (not shared) in
     * the services container (DI).
     *
     * @return void
     */
    public function register()
    {
        $this->di->set(
            $this->serviceName,
            function () {
                $config = container('config')->cache;

                $driver  = $config->drivers->{$config->views};
                $adapter = '\Phalcon\Cache\Backend\\' . $driver->adapter;
                $default = [
                    'statsKey' => 'SVC:'.substr(md5($config->prefix), 0, 16).'_',
                    'prefix'   => 'PVC_'.$config->prefix,
                ];

                return new $adapter(
                    new Output(['lifetime' => $config->lifetime]),
                    array_merge($driver->toArray(), $default)
                );
            }
        );
    }
}
