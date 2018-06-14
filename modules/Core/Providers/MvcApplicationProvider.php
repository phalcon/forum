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

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Application as PhApplication;
use Phosphorum\Core\Mvc\Application;

/**
 * Phosphorum\Core\Providers\MvcApplicationProvider
 *
 * @package Phosphorum\Core\Providers
 */
class MvcApplicationProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            $application = new Application($container);

            $application->setDI($container);
            $application->setEventsManager($container->get('eventsManager'));

            return $application;
        };

        $container->setShared(PhApplication::class, $service);
    }
}
