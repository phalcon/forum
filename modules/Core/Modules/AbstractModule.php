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

namespace Phosphorum\Core\Modules;

use Phalcon\Registry;
use Phalcon\DiInterface;
use Phalcon\Events\ManagerInterface;
use Phosphorum\Core\ModuleInterface;
use Phosphorum\Core\ServiceRegistrator;

/**
 * Phosphorum\Core\Modules\AbstractModule
 *
 * @package Phosphorum\Core\Modules
 */
abstract class AbstractModule implements ModuleInterface
{
    /** @var DiInterface */
    protected $container;

    /** @var  ServiceRegistrator */
    protected $serviceRegistrator;

    /** @var Registry */
    protected $registry;

    /**
     * Module constructor.
     *
     * @param DiInterface      $container
     * @param ManagerInterface $eventManager
     */
    public function __construct(DiInterface $container, ManagerInterface $eventManager)
    {
        $this->setBaseServices($container);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $path
     *
     * @return string
     */
    public function getPath(string $path = ''): string
    {
        $basePath = $this->registry->offsetGet('paths')->modules . DIRECTORY_SEPARATOR . $this->getName();

        return $basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param DiInterface|null $container
     */
    public function registerAutoloaders(DiInterface $container = null)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function registerServices(DiInterface $container)
    {
    }

    /**
     * Setting up base services.
     *
     * @param DiInterface $container
     */
    protected function setBaseServices(DiInterface $container)
    {
        $this->container = $container;
        $this->registry = $this->container->get(Registry::class);
        $this->serviceRegistrator = $container->get(ServiceRegistrator::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return ServiceRegistrator
     */
    public function getServiceRegistrator(): ServiceRegistrator
    {
        return $this->serviceRegistrator;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return sprintf('Phosphorum\%s\Mvc\Controllers', $this->getName());
    }
}
