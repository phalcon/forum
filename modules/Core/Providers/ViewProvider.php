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
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View\Simple;
use Phosphorum\Core\Environment;
use Phosphorum\Core\Events\ViewListener;

/**
 * Phosphorum\Core\Providers\ViewProvider
 *
 * @package Phosphorum\Core\Providers
 */
class ViewProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared('view', $service);
    }

    protected function createService(DiInterface $container)
    {
        return function () use ($container) {
            /** @var Environment $env */
            $env = $container->get(Environment::class);

            if ($env->isCommandLineInterface()) {
                $view = new Simple();
            } else {
                $view = new View();
            }

            $view->registerEngines([
                '.volt' => $container->get(Volt::class, [$view, $container]),
                '.php'  => Php::class,
            ]);

            $eventsManager = $container->get('eventsManager');
            $eventsManager->attach('view', new ViewListener($container));

            $view->setEventsManager($eventsManager);

            $view->setVars([
                'config' => $container->get(Config::class)
            ]);

            return $view;
        };
    }
}
