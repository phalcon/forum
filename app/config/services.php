<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DatabaseConnection;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Cache\Backend\File as FileCache;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Queue\Beanstalk;
use Ciconia\Ciconia;

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set(
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
    },
    true
);

/**
 * Setting up volt
 */
$di->set(
    'volt',
    function ($view, $di) use ($config) {

        $volt = new Volt($view, $di);

        $volt->setOptions(
            array(
                "compiledPath"      => APP_PATH . "/app/cache/volt/",
                "compiledSeparator" => "_",
                "compileAlways"     => $config->application->debug
            )
        );

        return $volt;
    },
    true
);

/**
 * Setting up the view component
 */
$di->set(
    'view',
    function () use ($config) {

        $view = new View();

        $view->setViewsDir($config->application->viewsDir);

        $view->registerEngines(
            array(
                ".volt" => 'volt'
            )
        );

        return $view;
    },
    true
);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set(
    'db',
    function () use ($config) {

        $connection = new DatabaseConnection($config->database->toArray());

        $debug = $config->application->debug;
        if ($debug) {

            $eventsManager = new EventsManager();

            $logger = new FileLogger(APP_PATH . "/app/logs/db.log");

            //Listen all the database events
            $eventsManager->attach(
                'db',
                function ($event, $connection) use ($logger) {
                    /** @var Phalcon\Events\Event $event */
                    if ($event->getType() == 'beforeQuery') {
                        /** @var DatabaseConnection $connection */
                        $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                }
            );

            //Assign the eventsManager to the db adapter instance
            $connection->setEventsManager($eventsManager);
        }

        return $connection;
    }
);

/**
 * Queue to deliver e-mails in real-time
 */
$di->set(
    'queue',
    function () use ($config) {
        return new Beanstalk(array(
            'host' => $config->beanstalk->host
        ));
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

        return new MetaDataAdapter(array(
            'metaDataDir' => APP_PATH . '/app/cache/metaData/'
        ));

    },
    true
);

/**
 * Start the session the first time some component request the session service
 */
$di->set(
    'session',
    function () {
        $session = new SessionAdapter();
        $session->start();
        return $session;
    },
    true
);

/**
 * Router
 */
$di->set(
    'router',
    function () {
        return include APP_PATH . "/app/config/routes.php";
    },
    true
);

/**
 * Register the configuration itself as a service
 */
$di->set('config', $config);

/**
 * Register the flash service with the Twitter Bootstrap classes
 */
$di->set(
    'flash',
    function () {
        return new Phalcon\Flash\Direct(array(
           'error'   => 'alert alert-danger',
           'success' => 'alert alert-success',
           'notice'  => 'alert alert-info',
        ));
    }
);

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set(
    'flashSession',
    function () {
        return new Phalcon\Flash\Session(array(
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
        ));
    }
);

$di->set(
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
$di->set(
    'viewCache',
    function () use ($config) {

        if ($config->application->debug) {
            $frontCache = new \Phalcon\Cache\Frontend\None();
            return new Phalcon\Cache\Backend\Memory($frontCache);
        } else {
            //Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Output(array(
                "lifetime" => 86400 * 30
            ));

            return new FileCache($frontCache, array(
                "cacheDir" => APP_PATH . "/app/cache/views/",
                "prefix"   => "forum-cache-"
            ));
        }
    }
);

/**
 * Cache
 */
$di->set(
    'modelsCache',
    function () use ($config) {

        if ($config->application->debug) {

            $frontCache = new \Phalcon\Cache\Frontend\None();
            return new Phalcon\Cache\Backend\Memory($frontCache);
        } else {

            //Cache data for one day by default
            $frontCache = new \Phalcon\Cache\Frontend\Data(array(
                "lifetime" => 86400 * 30
            ));

            return new \Phalcon\Cache\Backend\File($frontCache, array(
                "cacheDir" => APP_PATH . "/app/cache/data/",
                "prefix"   => "forum-cache-data-"
            ));
        }
    }
);

$di->set(
    'markdown',
    function () {
        $ciconia = new Ciconia();
        $ciconia->addExtension(new \Phosphorum\Markdown\TableExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\MentionExtension());
        $ciconia->addExtension(new \Phosphorum\Markdown\BlockQuoteExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\FencedCodeBlockExtension());
        $ciconia->addExtension(new \Ciconia\Extension\Gfm\UrlAutoLinkExtension());
        return $ciconia;
    },
    true
);
