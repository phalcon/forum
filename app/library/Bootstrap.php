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

namespace Phosphorum;

use Phalcon\Di;
use Dotenv\Dotenv;
use Phalcon\Config;
use Ciconia\Ciconia;
use Phalcon\Di\Service;
use Phosphorum\Markdown;
use Phalcon\Breadcrumbs;
use Phalcon\DiInterface;
use Phosphorum\Providers;
use Phalcon\Avatar\Gravatar;
use InvalidArgumentException;
use Phalcon\Di\FactoryDefault;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Error\Handler as ErrorHandler;
use Elasticsearch\Client as ElasticClient;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Application as MvcApplication;
use Phosphorum\Providers\ServiceProviderInterface;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;
use Phosphorum\Notifications\Checker as NotificationsChecker;

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

    private $loaders = [
        'markdown',
        'notifications',
        'flash',
        'elastic',
        'gravatar',
        'timezones',
        'breadcrumbs',
        'recaptcha',
    ];

    /**
     * Bootstrap constructor.
     *
     * @param string $mode The application mode: "normal", "cli", "api".
     */
    public function __construct($mode = 'normal')
    {
        $dotenv = new Dotenv(realpath(BASE_DIR));
        $dotenv->load();

        $this->di = new FactoryDefault;
        $this->app = $this->createInternalApplication($mode);

        $this->di->setShared('dotenv', $dotenv);
        $this->di->setShared('bootstrap', $this);

        Di::setDefault($this->di);

        /**
         * This service should be registered first
         */
        $this->initializeServiceProvider(new Providers\EventsManager\ServiceProvider($this->di));

        /** @noinspection PhpIncludeInspection */
        $providers = require config_path('providers.php');
        if (is_array($providers)) {
            $this->initializeServiceProviders($providers);
        }

        ErrorHandler::register();

        foreach ($this->loaders as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}();
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
     * Initialize the Markdown renderer.
     */
    protected function initMarkdown()
    {
        $this->di->setShared('markdown', function () {
            $ciconia = new Ciconia;

            $ciconia->addExtension(new Markdown\UnderscoredUrlsExtension);
            $ciconia->addExtension(new Markdown\TableExtension);
            $ciconia->addExtension(new Markdown\MentionExtension);
            $ciconia->addExtension(new Markdown\BlockQuoteExtension);
            $ciconia->addExtension(new Markdown\UrlAutoLinkExtension);
            //$ciconia->addExtension(new Markdown\NewLineExtension);
            $ciconia->addExtension(new FencedCodeBlockExtension);

            return $ciconia;
        });
    }

    /**
     * Initialize the real-time notifications checker.
     */
    protected function initNotifications()
    {
        $this->di->setShared('notifications', function () {
            return new NotificationsChecker();
        });
    }

    /**
     * Initialize the Breadcrumbs component.
     */
    protected function initBreadcrumbs()
    {
        $this->di->setShared('breadcrumbs', function () {
            /** @var DiInterface $this */
            $em = container('eventsManager');

            $breadcrumbs = new Breadcrumbs;
            $breadcrumbs->setEventsManager($em);
            $breadcrumbs->setSeparator('');

            return $breadcrumbs;
        });
    }

    /**
     * Initialize the Flash Service.
     *
     * Register the Flash Service with the Twitter Bootstrap classes
     */
    protected function initFlash()
    {
        $this->di->setShared('flash', function () {
            return new Flash(
                [
                    'error'   => 'alert alert-danger fade in',
                    'success' => 'alert alert-success fade in',
                    'notice'  => 'alert alert-info fade in',
                    'warning' => 'alert alert-warning fade in',
                ]
            );
        });

        $this->di->setShared('flashSession', function () {
            return new FlashSession([
                'error'   => 'alert alert-danger fade in',
                'success' => 'alert alert-success fade in',
                'notice'  => 'alert alert-info fade in',
                'warning' => 'alert alert-warning fade in',
            ]);
        });
    }

    /**
     * Initialize the Elasticsearch Service.
     */
    protected function initElastic()
    {
        $this->di->setShared('elastic', function () {
            /**
             * @var DiInterface $this
             * @var Config $config
             */
            $config = container('config')->get('elasticsearch', new Config);
            $hosts  = $config->get('hosts', new Config)->toArray();

            if (empty($hosts)) {
                // Fallback
                $hosts = ['127.0.0.1:9200'];
            }

            return new ElasticClient(['hosts' => $hosts]);
        });
    }

    /**
     * Initialize the Gravatar Service.
     */
    protected function initGravatar()
    {
        $this->di->setShared('gravatar', function () {
            /** @var  DiInterface $this $config */
            $config = container('config');

            return new Gravatar($config->get('gravatar', new Config));
        });
    }

    /**
     * Initialize time zones.
     */
    protected function initTimezones()
    {
        $this->di->setShared('timezones', function () {
            /** @noinspection PhpIncludeInspection */
            return require_once config_path('timezones.php');
        });
    }

    protected function initRecaptcha()
    {
        $this->di->setShared(
            'recaptcha',
            function () {
                return new ReCaptcha;
            }
        );
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
                throw new InvalidArgumentException(
                    'Not implemented yet.'
                );
            case 'api':
                throw new InvalidArgumentException(
                    'Not implemented yet.'
                );
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid application mode. Expected either "normal" either "cli" or "api". Got %s',
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
            $service = new Service($abstract, $concrete, true);
            $this->di->setRaw($abstract, $service);
        }

        return $this;
    }
}
