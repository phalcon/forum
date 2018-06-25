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

use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Di\ServiceProviderInterface;

/**
 * Phosphorum\Core\Providers\ModelsManagerProvider
 *
 * @package Phosphorum\Core\Providers
 */
class ModelsManagerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            $manager = new Manager();

            $manager->setDI($container);
            $manager->setEventsManager($container->get('eventsManager'));

            return $manager;
        };

        $container->setShared('modelsManager', $service);
    }
}
