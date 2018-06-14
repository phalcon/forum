<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Providers;

use Phalcon\Cli\Dispatcher as CliDi;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher as MvcDi;
use Phosphorum\Core\Environment;

/**
 * Phosphorum\Core\Providers\DispatcherProvider
 *
 * @package Phosphorum\Core\Providers
 */
class DispatcherProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared('dispatcher', $service);
    }

    protected function createService(DiInterface $container)
    {
        return function () use ($container) {
            /** @var Environment $env */
            $env = $container->get(Environment::class);

            if ($env->isCommandLineInterface()) {
                $dispatcher = new CliDi();
            } else {
                $dispatcher = new MvcDi();
            }

            $dispatcher->setDI($container);

            // TODO:
            // Attach DispatcherListener
            $eventsManager = $container->get('eventsManager');

            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        };
    }
}
