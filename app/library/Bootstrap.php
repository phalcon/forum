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
use Phalcon\Mvc\View;
use Phalcon\Di\Service;
use Phalcon\Mvc\Router;
use Phosphorum\Markdown;
use Phalcon\Breadcrumbs;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phosphorum\Providers;
use Phosphorum\Utils\Slug;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Queue\Beanstalk;
use Phalcon\Avatar\Gravatar;
use InvalidArgumentException;
use Phosphorum\Utils\Security;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Engine\Php;
use Phosphorum\Queue\DummyServer;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Error\Handler as ErrorHandler;
use Elasticsearch\Client as ElasticClient;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View\Exception as ViewException;
use Phosphorum\Providers\ServiceProviderInterface;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;
use Phalcon\Logger\AdapterInterface as LoggerInterface;
use Phalcon\Queue\Beanstalk\Exception as BeanstalkException;
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
        'logger',
        'security',
        'session',
        'view',
        'database',
        'queue',
        'router',
        'dispatcher',
        'slug',
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
     * Initialize the Logger.
     */
    protected function initLogger()
    {
        ErrorHandler::register();

        $this->di->set('logger', function ($filename = null, $format = null) {
            /** @var DiInterface $this */
            $config = container('config');

            $format   = $format ?: $config->get('logger')->format;
            $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
            $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

            if (false === strpos($filename, '.log')) {
                $filename = "$filename.log";
            }

            $formatter = new FormatterLine($format, $config->get('logger')->date);
            $logger = new FileLogger($path . $filename);

            $logger->setFormatter($formatter);
            $logger->setLogLevel($config->get('logger')->logLevel);

            return $logger;
        });
    }

    /**
     * Initialize the Security Service.
     */
    protected function initSecurity()
    {
        $this->di->setShared('security', function () {
            $security = new Security;
            $security->setWorkFactor(12);

            return $security;
        });
    }

    /**
     * Initialize the Session Service.
     *
     * Start the session the first time some component request the session service.
     */
    protected function initSession()
    {
        $this->di->setShared('session', function () {
            /** @var DiInterface $this */
            $config = container('config');

            $adapter = '\Phalcon\Session\Adapter\\' . $config->get('session')->adapter;

            /** @var \Phalcon\Session\AdapterInterface $session */
            $session = new $adapter;
            $session->start();

            return $session;
        });
    }

    /**
     * Initialize the View.
     *
     * Setting up the view component.
     */
    protected function initView()
    {
        $this->di->set('view', function () {
            /** @var DiInterface $this */
            $config = container('config');
            $em     = container('eventsManager');

            $view  = new View;
            $view->registerEngines([
                // Setting up Volt Engine
                '.volt' => function ($view, $di) {
                    /** @var DiInterface $this */
                    $config = container('config');
                    $volt = new VoltEngine($view, $di);

                    $options = [
                        'compiledPath' => function ($templatePath) {
                            /** @var DiInterface $this */
                            $config = container('config')->get('volt')->toArray();

                            $filename = str_replace(
                                ['\\', '/'],
                                $config['separator'],
                                trim(substr($templatePath, strlen(BASE_DIR)), '\\/')
                            );

                            $filename = basename($filename, '.volt') . $config['compiledExt'];
                            $cacheDir = rtrim($config['cacheDir'], '\\/') . DIRECTORY_SEPARATOR;

                            if (!is_dir($cacheDir)) {
                                @mkdir($cacheDir, 0755, true);
                            }

                            return rtrim($config['cacheDir'], '\\/') . DIRECTORY_SEPARATOR . $filename;
                        },
                        'compileAlways' => boolval($config->get('volt')->forceCompile),
                    ];

                    $volt->setOptions($options);

                    $volt->getCompiler()->addFunction('number_format', function ($resolvedArgs) {
                        return 'number_format(' . $resolvedArgs . ')';
                    })->addFunction('chr', function ($resolvedArgs) {
                        return 'chr(' . $resolvedArgs . ')';
                    });

                    return $volt;
                },
                // Setting up Php Engine
                '.phtml' => Php::class
            ]);

            $view->setViewsDir($config->get('application')->viewsDir);

            $that = $this;
            $em->attach('view', function ($event, $view) use ($that, $config) {
                /**
                 * @var LoggerInterface $logger
                 * @var View $view
                 * @var Event $event
                 * @var DiInterface $that
                 */
                $logger = $that->get('logger');
                $logger->debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()));

                if ('notFoundView' == $event->getType()) {
                    $message = sprintf('View not found: %s', $view->getActiveRenderPath());
                    $logger->error($message);
                    throw new ViewException($message);
                }
            });

            $view->setEventsManager($em);

            return $view;
        });
    }

    /**
     * Initialize the Database connection.
     *
     * Database connection is created based in the parameters defined in the configuration file.
     */
    protected function initDatabase()
    {
        $this->di->setShared('db', function () {
            /** @var DiInterface $this */
            $config = container('config')->get('database')->toArray();
            $em     = container('eventsManager');
            $that   = $this;

            $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
            unset($config['adapter']);

            /** @var \Phalcon\Db\Adapter\Pdo $connection */
            $connection = new $adapter($config);

            // Listen all the database events
            $em->attach(
                'db',
                function ($event, $connection) use ($that) {
                    /**
                     * @var \Phalcon\Events\Event $event
                     * @var \Phalcon\Db\AdapterInterface $connection
                     * @var DiInterface $that
                     */
                    if ($event->getType() == 'beforeQuery') {
                        $variables = $connection->getSQLVariables();
                        $string    = $connection->getSQLStatement();

                        if ($variables) {
                            $string .= ' [' . join(',', $variables) . ']';
                        }

                        // To disable logging change logLevel in config
                        $that->get('logger', ['db'])->debug($string);
                    }
                }
            );

            // Assign the eventsManager to the db adapter instance
            $connection->setEventsManager($em);

            return $connection;
        });

        $this->di->setShared('modelsManager', function () {
            /** @var DiInterface $this */
            $em = container('eventsManager');

            $modelsManager = new ModelsManager;
            $modelsManager->setEventsManager($em);

            return $modelsManager;
        });

        $this->di->setShared('modelsMetadata', function () {
            /** @var DiInterface $this */
            $config = container('config');

            $config = $config->get('metadata')->toArray();
            $adapter = '\Phalcon\Mvc\Model\Metadata\\' . $config['adapter'];
            unset($config['adapter']);

            $metaData = new $adapter($config);

            return $metaData;
        });
    }

    /**
     * Initialize the Queue Service.
     *
     * Queue to deliver e-mails in real-time and other tasks.
     */
    protected function initQueue()
    {
        $this->di->setShared(
            'queue',
            function () {
                $config = container('config');

                $config = $config->get('beanstalk');

                if (!$config->get('enabled')) {
                    return new DummyServer();
                }

                if (!$host = $config->get('host')) {
                    throw new BeanstalkException('Beanstalk is not configured');
                }

                return new Beanstalk(['host' => $host]);
            }
        );
    }

    /**
     * Initialize the Router.
     */
    protected function initRouter()
    {
        $this->di->setShared('router', function () {
            /**
             * @var DiInterface $this
             * @var \Phalcon\Mvc\Router $router
             */
            $em     = container('eventsManager');
            $router = include BASE_DIR . 'app/config/routes.php';

            if (!isset($_GET['_url'])) {
                $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
            }

            $router->removeExtraSlashes(true);
            $router->setEventsManager($em);

            $router->setDefaultNamespace('\Phosphorum\Controllers');
            $router->notFound(['controller' => 'error', 'action' => 'route404']);

            return $router;
        });
    }

    /**
     * Initialize the Dispatcher.
     */
    protected function initDispatcher()
    {
        $this->di->setShared('dispatcher', function () {
            /** @var DiInterface $this */
            $em = container('eventsManager');

            $dispatcher = new Dispatcher;

            $dispatcher->setDefaultNamespace('Phosphorum\Controllers');
            $dispatcher->setEventsManager($em);

            return $dispatcher;
        });
    }

    /**
     * Initialize the Slug component.
     */
    protected function initSlug()
    {
        $this->di->setShared('slug', ['className' => Slug::class]);
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
