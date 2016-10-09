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

use Phalcon\Config;
use Phalcon\Loader;
use Ciconia\Ciconia;
use Phalcon\Mvc\View;
use RuntimeException;
use Phalcon\Mvc\Router;
use Phosphorum\Markdown;
use Phalcon\Breadcrumbs;
use Phalcon\DiInterface;
use Phosphorum\ReCaptcha;
use Phalcon\Events\Event;
use Phosphorum\Utils\Slug;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Queue\Beanstalk;
use Phalcon\Avatar\Gravatar;
use Phosphorum\Utils\Security;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Engine\Php;
use Phosphorum\Queue\DummyServer;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Error\Handler as ErrorHandler;
use Elasticsearch\Client as ElasticClient;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Cache\Frontend\Output as FrontOutput;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;
use Phalcon\Logger\AdapterInterface as LoggerInterface;
use Phalcon\Queue\Beanstalk\Exception as BeanstalkException;
use Phosphorum\Notifications\Checker as NotificationsChecker;

class Bootstrap
{
    /** @var \Phalcon\Application  */
    private $app;

    /** @var  DiInterface */
    private $di;

    private $loaders = [
        'eventsManager',
        'config',
        'logger',
        'loader',
        'cache',
        'security',
        'session',
        'view',
        'database',
        'queue',
        'router',
        'url',
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
     */
    public function __construct()
    {
        $this->di = new FactoryDefault;
        $this->app = new Application;

        foreach ($this->loaders as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}();
        }

        $this->app->setEventsManager($this->di->getShared('eventsManager'));

        $this->di->setShared('app', $this->app);
        $this->app->setDI($this->di);
    }

    /**
     * Runs the Application
     *
     * @return \Phalcon\Application|string
     */
    public function run()
    {
        if (ENV_TESTING === APPLICATION_ENV) {
            return $this->app;
        }

        return $this->getOutput();
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        if ($this->app instanceof Application) {
            return $this->app->handle()->getContent();
        }

        return $this->app->handle();
    }

    /**
     * Initialize the Application Events Manager.
     */
    protected function initEventsManager()
    {
        $this->di->setShared('eventsManager', function () {
            $em = new EventsManager;
            $em->enablePriorities(true);

            return $em;
        });
    }

    /**
     * Initialize the Logger.
     */
    protected function initLogger()
    {
        ErrorHandler::register();

        $this->di->set('logger', function ($filename = null, $format = null) {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

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
     * Initialize the Loader.
     *
     * Adds all required namespaces.
     */
    protected function initLoader()
    {
        $config = $this->di->getShared('config');
        $loader = new Loader;
        $loader->registerNamespaces(
            [
                'Phosphorum\Models'      => $config->get('application')->modelsDir,
                'Phosphorum\Controllers' => $config->get('application')->controllersDir,
                'Phosphorum'             => $config->get('application')->libraryDir
            ]
        );

        $loader->register();
        $this->di->setShared('loader', $loader);
    }

    /**
     * Initialize the Cache.
     *
     * The frontend must always be Phalcon\Cache\Frontend\Output and the service 'viewCache'
     * must be registered as always open (not shared) in the services container (DI).
     */
    protected function initCache()
    {
        $this->di->set('viewCache', function () {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

            $frontend = new FrontOutput(['lifetime' => $config->get('viewCache')->lifetime]);

            $config  = $config->get('viewCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime']);

            return new $backend($frontend, $config);
        });

        $this->di->setShared('modelsCache', function () {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('modelsCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('modelsCache')->lifetime]);

            $config  = $config->get('modelsCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
        });

        $this->di->setShared('dataCache', function () {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('dataCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('dataCache')->lifetime]);

            $config  = $config->get('dataCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
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
            $config = $this->getShared('config');

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
            $config = $this->getShared('config');
            $em     = $this->getShared('eventsManager');

            $view  = new View;
            $view->registerEngines([
                // Setting up Volt Engine
                '.volt' => function ($view, $di) {
                    /** @var DiInterface $this */
                    $config = $this->getShared('config');
                    $volt = new VoltEngine($view, $di);

                    $options = [
                        'compiledPath' => function ($templatePath) {
                            /** @var DiInterface $this */
                            $config = $this->getShared('config')->get('volt')->toArray();

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
            $config = $this->getShared('config')->get('database')->toArray();
            $em     = $this->getShared('eventsManager');
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
            $em = $this->getShared('eventsManager');

            $modelsManager = new ModelsManager;
            $modelsManager->setEventsManager($em);

            return $modelsManager;
        });

        $this->di->setShared('modelsMetadata', function () {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

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
                $config = $this->getShared('config');

                $config = $config->get('beanstalk');
                $config->get('disabled', true);

                if ($config->get('disabled', true)) {
                    return new DummyServer();
                }

                if (!$host = $config->get('host', false)) {
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
            $em     = $this->getShared('eventsManager');
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
     * Initialize the Url service.
     *
     * The URL component is used to generate all kind of urls in the application.
     */
    protected function initUrl()
    {
        $this->di->setShared('url', function () {
            /** @var DiInterface $this */
            $config = $this->getShared('config');

            $url = new UrlResolver;

            if (ENV_PRODUCTION === APPLICATION_ENV) {
                $url->setBaseUri($config->get('application')->production->baseUri);
                $url->setStaticBaseUri($config->get('application')->production->staticBaseUri);
            } else {
                $url->setBaseUri($config->get('application')->development->baseUri);
                $url->setStaticBaseUri($config->get('application')->development->staticBaseUri);
            }

            return $url;
        });
    }

    /**
     * Initialize the Dispatcher.
     */
    protected function initDispatcher()
    {
        $this->di->setShared('dispatcher', function () {
            /** @var DiInterface $this */
            $em = $this->getShared('eventsManager');

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
            $em = $this->getShared('eventsManager');

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
            $config = $this->getShared('config')->get('elasticsearch', new Config);
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
            $config = $this->getShared('config');

            return new Gravatar($config->get('gravatar', new Config));
        });
    }

    /**
     * Initialize time zones.
     */
    protected function initTimezones()
    {
        $this->di->setShared('timezones', function () {
            return require_once BASE_DIR . 'app/config/timezones.php';
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
     * Initialize the Application Config.
     */
    protected function initConfig()
    {
        $this->di->setShared('config', function () {
            $path = BASE_DIR . 'app/config/';

            if (!is_readable($path . 'config.php')) {
                throw new RuntimeException(
                    'Unable to read config from ' . $path . 'config.php'
                );
            }

            $config = include $path . 'config.php';

            if (is_array($config)) {
                $config = new Config($config);
            }

            if (!$config instanceof Config) {
                $type = gettype($config);
                if ($type == 'boolean') {
                    $type .= ($type ? ' (true)' : ' (false)');
                } elseif (is_object($type)) {
                    $type = get_class($type);
                }

                throw new RuntimeException(
                    sprintf(
                        'Unable to read config file. Config must be either an array or Phalcon\Config instance. Got %s',
                        $type
                    )
                );
            }

            if (is_readable($path . APPLICATION_ENV . '.php')) {
                $override = include_once $path . APPLICATION_ENV . '.php';

                if (is_array($override)) {
                    $override = new Config($override);
                }

                if ($override instanceof Config) {
                    $config->merge($override);
                }
            }

            return $config;
        });
    }
}
