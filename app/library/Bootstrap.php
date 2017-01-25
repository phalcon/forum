<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2017 Phalcon Team and contributors                  |
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
use Phalcon\DiInterface;
use Phosphorum\Provider;
use InvalidArgumentException;
use Phalcon\Di\FactoryDefault;
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
    private $mode;

    /**
     * The Dependency Injection Container
     * @var DiInterface
     */
    private $di;

    /**
     * Current application environment:
     * production, staging, development, testing
     * @var string
     */
    private $environment;

    /**
     * Bootstrap constructor.
     *
     * @param string $mode The application mode: "normal", "cli", "api".
     */
    public function __construct($mode = 'normal')
    {
        $this->mode = $mode;

        $this->di = new FactoryDefault();

        $this->di->setShared('bootstrap', $this);

        Di::setDefault($this->di);

        /**
         * These services should be registered first
         */
        $this->initializeServiceProvider(new Provider\EventsManager\ServiceProvider($this->di));
        $this->setupEnvironment();
        $this->initializeServiceProvider(new Provider\ErrorHandler\ServiceProvider($this->di));

        $this->createInternalApplication();

        /** @noinspection PhpIncludeInspection */
        $providers = require config_path('providers.php');
        if (is_array($providers)) {
            $this->initializeServiceProviders($providers);
        }

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
     * @return mixed
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
     * Gets current application environment: production, staging, development, testing, etc.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Gets current application mode: normal, cli, api.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
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
     * @throws InvalidArgumentException
     */
    protected function createInternalApplication()
    {
        switch ($this->mode) {
            case 'normal':
                $this->app = new MvcApplication($this->di);
                break;
            case 'cli':
                $this->app = new Console($this->di);
                break;
            case 'api':
                throw new InvalidArgumentException(
                    'Not implemented yet.'
                );
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid application mode. Expected either "normal" or "cli" or "api". Got "%s".',
                        is_scalar($this->mode) ? $this->mode : var_export($this->mode, true)
                    )
                );
        }
    }

    /**
     * Setting up the application environment.
     *
     * This tries to get `APP_ENV` environment variable from $_ENV.
     * If failed the `development` will be used.
     *
     * After getting `APP_ENV` variable we set the Bootstrap::$environment
     * and the `APPLICATION_ENV` constant.
     */
    protected function setupEnvironment()
    {
        $this->environment = env('APP_ENV', 'development');

        defined('APPLICATION_ENV') || define('APPLICATION_ENV', $this->environment);

        $this->initializeServiceProvider(new Provider\Environment\ServiceProvider($this->di));
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
