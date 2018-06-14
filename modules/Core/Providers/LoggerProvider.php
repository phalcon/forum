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

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Registry;
use Phosphorum\Core\Logger\LoggerManager;

/**
 * Phosphorum\Core\Providers\LoggerProvider
 *
 * @package Phosphorum\Core\Providers
 */
class LoggerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            $manager = new LoggerManager();

            return $manager->create(
                $container->get(Registry::class),
                $container->get(Config::class)->get('logger', new Config())
            );
        };

        $container->set(AdapterInterface::class, $service);
    }
}
