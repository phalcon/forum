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

/**
 * @var $config \Phalcon\Config
 */

use Phalcon\Config;
use Phalcon\Logger;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DatabaseConnection;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Cache\Backend\File as FileCache;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Queue\Beanstalk;
use Phalcon\Cache\Frontend\None as FrontendNone;
use Phalcon\Cache\Backend\Memory as MemoryBackend;
use Phosphorum\Notifications\Checker as NotificationsChecker;
use Phosphorum\Queue\DummyServer;
use Phalcon\Cache\Frontend\Output as FrontendOutput;
use Phalcon\Avatar\Gravatar;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Flash\Direct as FlashDirect;
use Ciconia\Ciconia;

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared(
    'url',
    function () use ($config) {
        $url = new UrlResolver();
        if (!$config->application->debug) {
            $url->setBaseUri($config->application->production->baseUri);
            $url->setStaticBaseUri($config->application->production->staticBaseUri);
        } else {
            $url->setBaseUri($config->application->development->baseUri);
            $url->setStaticBaseUri($config->application->development->staticBaseUri);
        }
        return $url;
    }
);

/**
 * Setting up volt
 */
$di->setShared(
    'volt',
    function ($view, $di) use ($config) {

        $volt = new Volt($view, $di);

        $volt->setOptions(
            [
                "compiledPath"      => APP_PATH . "/app/cache/volt/",
                "compiledSeparator" => "_",
                "compileAlways"     => $config->application->debug
            ]
        );

        $compiler = $volt->getCompiler();

        $compiler->addFunction('number_format', function ($resolvedArgs) {
            return 'number_format(' . $resolvedArgs . ')';
        });

        $compiler->addFunction('chr', function ($resolvedArgs) {
            return 'chr(' . $resolvedArgs . ')';
        });

        return $volt;
    }
);

/**
 * Setting up the view component
 */
$di->setShared(
    'view',
    function () use ($config) {

        $view = new View();

        $view->setViewsDir($config->application->viewsDir);
        $view->registerEngines([".volt" => 'volt']);

        return $view;
    }
);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set(
    'db',
    function () use ($config, $di) {
        $connection    = new DatabaseConnection($config->database->toArray());
        $eventsManager = new EventsManager();

        // Listen all the database events
        $eventsManager->attach(
            'db',
            function ($event, $connection) use ($di) {
                /** @var Phalcon\Events\Event $event */
                if ($event->getType() == 'beforeQuery') {
                    /** @var DatabaseConnection $connection */
                    $variables = $connection->getSQLVariables();
                    $string    = $connection->getSQLStatement();

                    if ($variables) {
                        $string .= ' [' . join(',', $variables) . ']';
                    }

                    // To disable logging change logLevel in config
                    $di->get('logger', ['db.log'])->debug($string);
                }
            }
        );

        // Assign the eventsManager to the db adapter instance
        $connection->setEventsManager($eventsManager);

        return $connection;
    }
);

/**
 * Queue to deliver e-mails in real-time
 */
$di->set(
    'queue',
    function () use ($config) {
        if (isset($config->beanstalk->disabled) && $config->beanstalk->disabled) {
            return new DummyServer();
        }

        if (!isset($config->beanstalk->host)) {
            throw new \Exception('Beanstalk is not configured');
        }

        return new Beanstalk(['host' => $config->beanstalk->host]);
    },
    true
);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set(
    'modelsMetadata',
    function () use ($config) {

        if ($config->application->debug) {
            return new MemoryMetaDataAdapter();
        }

        return new MetaDataAdapter(['metaDataDir' => APP_PATH . '/app/cache/metaData/']);

    },
    true
);

/**
 * Start the session the first time some component request the session service
 */
$di->setShared(
    'session',
    function () {
        $session = new SessionAdapter();
        $session->start();
        return $session;
    }
);

/**
 * Router
 */
$di->setShared(
    'router',
    function () {
        return include APP_PATH . "/app/config/routes.php";
    }
);

/**
 * Register the configuration itself as a service
 */
$di->setShared('config', $config);

/**
 * Register the flash service with the Twitter Bootstrap classes
 */
$di->setShared(
    'flash',
    function () {
        return new FlashDirect([
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
            'warning' => 'alert alert-warning'
        ]);
    }
);

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->setShared(
    'flashSession',
    function () {
        return new FlashSession([
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
            'warning' => 'alert alert-warning'
        ]);
    }
);

/**
 * Register the Slug component
 */
$di->setShared(
    'slug',
    ['className' => '\Phosphorum\Utils\Slug']
);

$di->setShared(
    'dispatcher',
    function () {
        $dispatcher = new MvcDispatcher();
        $dispatcher->setDefaultNamespace('Phosphorum\Controllers');
        return $dispatcher;
    }
);

/**
 * View cache
 */
$di->setShared(
    'viewCache',
    function () use ($config) {
        if ($config->application->debug) {
            $frontCache = new FrontendNone();
            return new MemoryBackend($frontCache);
        } else {
            //Cache data for one day by default
            $frontCache = new FrontendOutput(["lifetime" => 86400 * 30]);

            return new FileCache($frontCache, [
                "cacheDir" => APP_PATH . "/app/cache/views/",
                "prefix"   => "forum-cache-"
            ]);
        }
    }
);

/**
 * Cache
 */
$di->setShared(
    'modelsCache',
    function () use ($config) {
        if ($config->application->debug) {
            $frontCache = new FrontendNone();
            return new MemoryBackend($frontCache);
        } else {
            //Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Data(["lifetime" => 86400 * 30]);

            return new \Phalcon\Cache\Backend\File($frontCache, [
                "cacheDir" => APP_PATH . "/app/cache/data/",
                "prefix"   => "forum-cache-data-"
            ]);
        }
    }
);

/**
 * Markdown renderer
 */
$di->setShared(
    'markdown',
    function () {
        $ciconia = new Ciconia();
        $ciconia->addExtension(new \Phosphorum\Markdown\UnderscoredUrlsExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\TableExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\MentionExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\BlockQuoteExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\UrlAutoLinkExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\FencedCodeBlockExtension());
        return $ciconia;
    }
);

/**
 * Real-Time notifications checker
 */
$di->setShared(
    'notifications',
    function () {
        return new NotificationsChecker();
    }
);

/**
 * Gravatar instance
 */
$di->setShared('gravatar', function () use ($config) {
    return new Gravatar($config->get('gravatar', new Config()));
});

/**
 * Logger service
 */
$di->set('logger', function ($filename = null, $format = null) use ($config) {
    $format   = $format ?: $config->get('logger')->format;
    $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});

$di->setShared('timezones', function () {
    return require APP_PATH .'/app/config/timezones.php';
});
