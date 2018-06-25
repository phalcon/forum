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

use Closure;
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ViewBaseInterface;
use Phosphorum\Core\Environment;
use Phosphorum\Core\Mvc\View\Engine\VoltManager;

/**
 * Phosphorum\Core\Providers\VoltProvider
 *
 * @package Phosphorum\Core\Providers
 */
class VoltProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared(Volt::class, $service);
    }

    protected function createService(DiInterface $container): Closure
    {
        return function (ViewBaseInterface $view, DiInterface $internalContainer = null) use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            $applicationConfig = $config->get('application');
            if ($applicationConfig instanceof Config == false) {
                $applicationConfig = new Config();
            }

            $manager = new VoltManager($container);

            return $manager->create(
                $container->get(Environment::class),
                $applicationConfig,
                $view,
                $internalContainer
            );
        };
    }
}
