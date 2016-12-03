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

namespace Phosphorum\Providers\ModelsCache;

use Phalcon\Cache\Frontend\Data;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\ModelsCache\ServiceProvider
 *
 * @package Phosphorum\Providers\ModelsCache
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'modelsCache';

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
                $config = container('config')->cache;

                $driver  = $config->drivers->{$config->default};
                $adapter = '\Phalcon\Cache\Backend\\' . $driver->adapter;

                return new $adapter(
                    new Data(['lifetime' => $config->lifetime]),
                    array_merge($driver->toArray(), ['prefix' => $config->prefix])
                );
            }
        );
    }
}
