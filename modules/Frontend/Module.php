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

namespace Phosphorum\Frontend;

use Phalcon\DiInterface;
use Phalcon\Events\ManagerInterface;
use Phosphorum\Core\Modules\AbstractModule;
use Phosphorum\Frontend\Events\ApplicationListener;
use Phosphorum\Frontend\Providers\ReCaptchaProvider;
use Phosphorum\Frontend\Providers\RouterProvider;
use Phosphorum\Frontend\Providers\ViewProvider;

/**
 * Phosphorum\Frontend\Module
 *
 * @package Phosphorum\Frontend
 */
class Module extends AbstractModule
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface      $container
     * @param ManagerInterface $eventManager
     */
    public function __construct(DiInterface $container, ManagerInterface $eventManager)
    {
        parent::__construct($container, $eventManager);

        $this->registerBaseServices();
        $this->registerBaseListeners();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Frontend';
    }

    /**
     * Register base services.
     *
     * @return void
     */
    protected function registerBaseServices(): void
    {
        $this->serviceRegistrator->registerService(
            new RouterProvider($this->getName(), $this->getDefaultNamespace())
        );
    }

    protected function registerBaseListeners(): void
    {
        $applicationListener = new ApplicationListener($this->getName());

        $this->eventManager->attach('application:boot', $applicationListener);
    }

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function registerServices(DiInterface $container)
    {
        parent::registerServices($container);

        $this->serviceRegistrator->registerService(
            new ViewProvider($this->getPath('resources/views'))
        );

        $this->serviceRegistrator->registerService(
            new ReCaptchaProvider()
        );
    }
}
