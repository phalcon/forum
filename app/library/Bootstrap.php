<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum;

use Phalcon\Di;
use Dotenv\Dotenv;
use Phalcon\DiInterface;
use Phosphorum\Provider;
use InvalidArgumentException;
use Phalcon\Di\FactoryDefault;
use Phalcon\Error\Handler as ErrorHandler;
use Phosphorum\Console\Application as Console;
use Phalcon\Mvc\Application as MvcApplication;
use Phosphorum\Provider\ServiceProviderInterface;

class Bootstrap
{
    /**
     * The internal application core.
     * @var \Phalcon\Application
     */
    private $app;

    /**
     * The application mode.
     * @var string
     */
    protected $mode;

    /** @var  DiInterface */
    private $di;

    /**
     * Bootstrap constructor.
     *
     * @param string $mode The application mode: "normal", "cli", "api".
     */
    public function __construct($mode = 'normal')
    {
        $dotenv = new Dotenv(realpath(BASE_DIR));
        $dotenv->load();

        $this->di = new FactoryDefault();
        $this->app = $this->createInternalApplication($mode);

        $this->di->setShared('dotenv', $dotenv);
        $this->di->setShared('bootstrap', $this);
        $this->di->setShared('mode', function () use ($mode) {
            return $mode;
        });

        Di::setDefault($this->di);

        /**
         * These services should be registered first
         */
        $this->initializeServiceProvider(new Provider\EventsManager\ServiceProvider($this->di));
        $this->initializeServiceProvider(new Provider\Environment\ServiceProvider($this->di));

        /** @noinspection PhpIncludeInspection */
        $providers = require config_path('providers.php');
        if (is_array($providers)) {
            $this->initializeServiceProviders($providers);
        }

        ErrorHandler::register();

        $this->app->setEventsManager(container('eventsManager'));

        $this->di->setShared('app', $this->app);
        $this->app->setDI($this->di);

        /** @noinspection PhpIncludeInspection */
        $services = require config_path('services.php');
        if (is_array($services)) {
            $this->initializeServices($services);
        }
    }

    /**
     * Runs the Application
     *
     * @return \Phalcon\Application|string
     */
    public function run()
    {
        return $this->getOutput();
    }

    /**
     * Get the Application.
     *
     * @return \Phalcon\Application|\Phalcon\Mvc\Micro
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        if ($this->app instanceof MvcApplication) {
            return $this->app->handle()->getContent();
        }

        return $this->app->handle();
    }

    /**
     * Initialize the Service Providers.
     *
     * @param  string[] $providers
     * @return $this
     */
    protected function initializeServiceProviders(array $providers)
    {
        foreach ($providers as $name => $class) {
            $this->initializeServiceProvider(new $class($this->di));
        }

        return $this;
    }

    /**
     * Initialize the Service Provider.
     *
     * Usually the Service Provider register a service in the Dependency Injector Container.
     *
     * @param  ServiceProviderInterface $serviceProvider
     * @return $this
     */
    protected function initializeServiceProvider(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register();
        $serviceProvider->boot();

        return $this;
    }

    /**
     * Create internal application to handle requests.
     *
     * @param  string $mode The application mode.
     * @return MvcApplication|\Phalcon\Cli\Console
     *
     * @throws InvalidArgumentException
     */
    protected function createInternalApplication($mode)
    {
        $this->mode = $mode;

        switch ($mode) {
            case 'normal':
                return new MvcApplication($this->di);
            case 'cli':
                return new Console($this->di);
            case 'api':
                throw new InvalidArgumentException(
                    'Not implemented yet.'
                );
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid application mode. Expected either "normal" either "cli" or "api". Got "%s".',
                        is_scalar($mode) ? $mode : var_export($mode, true)
                    )
                );
        }
    }

    /**
     * Register services in the Dependency Injector Container.
     * This allows to inject dependencies by using abstract classes.
     *
     * <code>
     * $services = [
     *     '\My\Namespace\Services\UserInterface' => '\My\Concrete\UserService',
     * ];
     *
     * $bootstrap->initializeModelServices($services)
     * </code>
     *
     * @param  string[] $services
     * @return $this
     */
    protected function initializeServices(array $services)
    {
        foreach ($services as $abstract => $concrete) {
            $this->di->setShared($abstract, $concrete);
        }

        return $this;
    }
}
