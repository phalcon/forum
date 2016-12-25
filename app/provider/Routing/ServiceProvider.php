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

namespace Phosphorum\Provider\Routing;

use Phalcon\Mvc\Router;
use InvalidArgumentException;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Routing\ServiceProvider
 *
 * @package Phosphorum\Provider\Routing
 */
class ServiceProvider extends AbstractServiceProvider
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
                $mode = container('bootstrap')->getMode();

                switch ($mode) {
                    case 'normal':
                        /** @noinspection PhpIncludeInspection */
                        $router = require config_path('routes.php');

                        if (!isset($_GET['_url'])) {
                            $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
                        }

                        $router->removeExtraSlashes(true);
                        $router->setEventsManager(container('eventsManager'));
                        $router->setDefaultNamespace('Phosphorum\Controller');
                        $router->notFound([
                            'controller' => 'error',
                            'action'     => 'route404',
                        ]);

                        break;
                    case 'cli':
                        /** @noinspection PhpIncludeInspection */
                        $router = require config_path('cli-routes.php');

                        break;
                    case 'api':
                        throw new InvalidArgumentException(
                            'Not implemented yet.'
                        );
                    default:
                        throw new InvalidArgumentException(
                            sprintf(
                                'Invalid application mode. Expected either "normal" or "cli" or "api". Got "%s".',
                                is_scalar($mode) ? $mode : var_export($mode, true)
                            )
                        );
                }

                $router->setDI(container());

                return $router;
            }
        );
    }
}
