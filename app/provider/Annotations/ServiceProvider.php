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

namespace Phosphorum\Provider\Annotations;

use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Annotations\ServiceProvider
 *
 * @package Phosphorum\Provider\Annotations
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'annotations';

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
                $config = container('config')->annotations;

                $driver  = $config->drivers->{$config->default};
                $adapter = '\Phalcon\Annotations\Extended\Adapter\\' . $driver->adapter;

                $default = [
                    'lifetime' => $config->lifetime,
                    'prefix'   => $config->prefix,
                ];

                return new $adapter(array_merge($driver->toArray(), $default));
            }
        );
    }
}
