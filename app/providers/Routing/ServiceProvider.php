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

namespace Phosphorum\Providers\Routing;

use Phalcon\Mvc\Router;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\Routing\ServiceProvider
 *
 * @package Phosphorum\Providers\Routing
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'router';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $em     = container('eventsManager');
                /** @noinspection PhpIncludeInspection */
                $router = require config_path('routes.php');

                if (!isset($_GET['_url'])) {
                    $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
                }

                $router->removeExtraSlashes(true);
                $router->setEventsManager($em);

                $router->setDefaultNamespace('\Phosphorum\Controllers');
                $router->notFound([
                    'controller' => 'error',
                    'action'     => 'route404',
                ]);

                return $router;
            }
        );
    }
}
