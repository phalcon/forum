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

namespace Phosphorum\Provider\Dispatcher;

use Phalcon\Mvc\Dispatcher;
use Phosphorum\Provider\AbstractServiceProvider;
use Phosphorum\Listener\DispatcherListener;

/**
 * Phosphorum\Provider\Dispatcher\ServiceProvider
 *
 * @package Phosphorum\Provider\Dispatcher
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'dispatcher';

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
                $dispatcher = new Dispatcher();

                container('eventsManager')->attach('dispatch', new DispatcherListener(container()));

                $dispatcher->setDefaultNamespace('Phosphorum\Controller');
                $dispatcher->setDI(container());
                $dispatcher->setEventsManager(container('eventsManager'));

                return $dispatcher;
            }
        );
    }
}
