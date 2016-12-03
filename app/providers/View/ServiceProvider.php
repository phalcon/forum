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

namespace Phosphorum\Providers\View;

use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Mvc\View\Engine\Php;
use Phosphorum\Providers\Abstrakt;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Logger\AdapterInterface as LoggerInterface;

/**
 * Phosphorum\Providers\View\ServiceProvider
 *
 * @package Phosphorum\Providers\View
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'view';

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
                $config = container('config')->application;

                $view = new View();

                $view->registerEngines([
                    '.volt' => container('volt', [$view, $this]),
                    '.php'  => Php::class,
                ]);

                $view->setViewsDir($config->viewsDir);

                $eventsManager = container('eventsManager');
                $eventsManager->attach('view:notFoundView', function ($event, $view) {
                    /**
                     * @var LoggerInterface $logger
                     * @var View $view
                     * @var Event $event
                     * @var DiInterface $that
                     */
                    $logger = container()->get('logger');
                    $logger->debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()));

                    if ('notFoundView' == $event->getType()) {
                        $message = sprintf('View not found: %s', $view->getActiveRenderPath());
                        $logger->error($message);

                        throw new ViewException($message);
                    }
                });

                $view->setEventsManager($eventsManager);

                return $view;
            }
        );
    }
}
