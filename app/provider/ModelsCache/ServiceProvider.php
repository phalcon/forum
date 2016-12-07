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

namespace Phosphorum\Provider\ModelsCache;

use Phalcon\Cache\Frontend\Data;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\ModelsCache\ServiceProvider
 *
 * @package Phosphorum\Provider\ModelsCache
 */
class ServiceProvider extends AbstractServiceProvider
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
                $default = [
                    'statsKey' => 'SMC:'.substr(md5($config->prefix), 0, 16).'_',
                    'prefix'   => 'PMC_'.$config->prefix,
                ];

                return new $adapter(
                    new Data(['lifetime' => $config->lifetime]),
                    array_merge($driver->toArray(), $default)
                );
            }
        );
    }
}
