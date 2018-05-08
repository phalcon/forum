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

namespace Phosphorum\Provider\Queue;

use Aws\Sdk;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Queue\ServiceProvider
 *
 * @package Phosphorum\Provider\Queue
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'queue';

    /**
     * {@inheritdoc}
     *
     * Queue to deliver e-mails in real-time and other tasks.
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config')->queue;
                $driver  = $config->drivers->{$config->default};

                if ($config->default === 'fake') {
                    return new Fake(null);
                }

                $sdk = new Sdk($driver->toArray());
                return $sdk->createClient($config->default);
            }
        );
    }
}
