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
use Phosphorum\Core\Session\SessionManager;

/**
 * Phosphorum\Core\Providers\SessionProvider
 *
 * @package Phosphorum\Core\Providers
 */
class SessionProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);
            $manager = new SessionManager();

            $session = $manager->create($config->get('session', new Config()));
            $session->start();

            return $session;
        };

        $container->setShared('session', $service);
    }
}
