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

namespace Phosphorum\Core;

use Core\Exceptions\InvalidArgumentException;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Registry;
use Phosphorum\Core\Modules\Manager;
use Phosphorum\Core\Modules\ManagerInterface;
use Phosphorum\Core\Providers\EventsManagerProvider;
use Phosphorum\Core\Providers\FileSystemProvider;
use Phosphorum\Core\Providers\RouterProvider;
use Phosphorum\Core\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\ServiceRegistrator
 *
 * @package Phosphorum\Core
 */
final class ServiceRegistrator implements InjectionAwareInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /**
     * The base path for the application.
     *
     * @var string
     */
    protected $basePath;

    /**
     * A list of the registered service providers.
     *
     * @var ServiceProviderInterface[]
     */
    public $serviceProviders = [];

    /**
     * Create a new Registrator instance.
     *
     * @param string|null      $basePath
     * @param DiInterface|null $container
     */
    public function __construct(string $basePath = null, DiInterface $container = null)
    {
        $this->__DiInject($container);

        $this->registerBaseBindings();
        $this->createEnvironment($basePath);
        $this->registerBaseServices();
    }

    /**
     * Registers the base bindings.
     *
     * @return void
     */
    protected function registerBaseBindings(): void
    {
        $this->getDI()->setShared(Registry::class, Registry::class);
        $this->getDI()->setShared(ServiceRegistrator::class, $this);
        $this->getDI()->setShared(ManagerInterface::class, function () {
            return new Manager($this);
        });
    }

    /**
     * Registers the base services.
     *
     * @return void
     */
    protected function registerBaseServices(): void
    {
        $this->registerService(new EventsManagerProvider());
        $this->registerService(new FileSystemProvider());
        $this->registerService(new RouterProvider());
    }

    /**
     * Creates and registers application environment.
     *
     * @param string|null $basePath
     * @return void
     */
    protected function createEnvironment(string $basePath = null): void
    {
        $environment = new Environment($basePath, $this->getDI());
        $this->getDI()->setShared(Environment::class, $environment);
    }

    /**
     * Registers a Service Provider in the internal stack.
     *
     * @param  ServiceProviderInterface|string $provider
     * @param  bool                            $force
     * @return ServiceProviderInterface
     */
    public function registerService($provider, $force = false): ServiceProviderInterface
    {
        $registered = $this->getServiceProviderByInstanceOf($provider);

        // This needed to prevent error when events manager is not registered yet
        if ($this->getDI()->has('eventsManager')) {
            $events = $this->getDI()->get('eventsManager');
            $events->fire('services:beforeRegister', $this, compact('provider'));
        }

        if ($registered && $force == false) {
            // Do not register twice
            return $provider;
        }

        if (is_string($provider)) {
            $provider = $this->instantiateProvider($provider);
        }

        $provider->register($this->getDI());
        $this->serviceProviders[] = $provider;

        // This needed to prevent error when events manager is not registered yet
        if ($this->getDI()->has('eventsManager')) {
            $events = $this->getDI()->get('eventsManager');
            $events->fire('services:afterRegister', $this, compact('provider'));
        }

        return $provider;
    }

    /**
     * Gets a Service Provider matched by provided instance (if any).
     *
     * @param  ServiceProviderInterface|string $serviceProvider
     * @return ServiceProviderInterface|null
     */
    public function getServiceProviderByInstanceOf($serviceProvider)
    {
        $providers = $this->getServiceProvidersByInstanceOf($serviceProvider);

        return count($providers) ? $providers[0] : null;
    }

    /**
     * Gets Service Providers matched by provided instance.
     *
     * @param  ServiceProviderInterface|string $serviceProvider
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getServiceProvidersByInstanceOf($serviceProvider)
    {
        $this->assertIsStringOrInstanceOfObject($serviceProvider);

        $className = is_string($serviceProvider) ? $serviceProvider : get_class($serviceProvider);

        return array_filter($this->serviceProviders, function ($name) use ($className) {
            return $name instanceof $className;
        });
    }

    /**
     * Instantiate a service provider
     *
     * @param  string $serviceProvider
     * @return ServiceProviderInterface
     */
    public function instantiateProvider(string $serviceProvider): ServiceProviderInterface
    {
        return new $serviceProvider();
    }

    /**
     * Checks if a Service Provider is registered in the current ServiceRegistrator instance.
     *
     * @param  ServiceProviderInterface|string $serviceProvider
     * @return bool
     */
    public function hasServiceProvider($serviceProvider)
    {
        return count($this->getServiceProvidersByInstanceOf($serviceProvider)) > 0;
    }

    protected function assertIsStringOrInstanceOfObject($serviceProvider)
    {
        if (is_string($serviceProvider) == false && is_object($serviceProvider) == false) {
            throw new InvalidArgumentException(
                'The $serviceProvider parameter must be either a string or an object instance.'
            );
        }
    }
}
