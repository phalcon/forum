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

namespace Phosphorum\Provider\ErrorHandler;

use Whoops\Run;
use InvalidArgumentException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Phosphorum\Exception\Handler\LoggerHandler;
use Phosphorum\Provider\AbstractServiceProvider;
use Phosphorum\Exception\Handler\ErrorPageHandler;

/**
 * Phosphorum\Provider\ErrorHandler\ServiceProvider
 *
 * @package Phosphorum\Provider\Environment
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'errorHandler';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared("{$this->serviceName}.loggerHandler", LoggerHandler::class);
        $this->di->setShared("{$this->serviceName}.prettyPageHandler", PrettyPageHandler::class);
        $this->di->setShared("{$this->serviceName}.errorPageHandler", ErrorPageHandler::class);

        $this->di->setShared(
            "{$this->serviceName}.jsonResponseHandler",
            function () {
                $handler = new JsonResponseHandler();
                $handler->setJsonApi(true);

                return $handler;
            }
        );

        $service = $this->serviceName;

        $this->di->setShared(
            $this->serviceName,
            function () use ($service) {
                $run  = new Run();
                $run->pushHandler(container("{$service}.loggerHandler"));

                $mode = container('bootstrap')->getMode();

                switch ($mode) {
                    case 'normal':
                        if (env('APP_DEBUG', false)) {
                            $run->pushHandler(singleton("{$service}.prettyPageHandler"));
                        } else {
                            $run->pushHandler(singleton("{$service}.errorPageHandler"));
                        }
                        break;
                    case 'cli':
                        // @todo
                        break;
                    case 'api':
                        $run->pushHandler(singleton("{$service}.jsonResponseHandler"));
                        throw new InvalidArgumentException(
                            'Not implemented yet.'
                        );
                        break;
                    default:
                        throw new InvalidArgumentException(
                            sprintf(
                                'Invalid application mode. Expected either "normal" or "cli" or "api". Got "%s".',
                                is_scalar($mode) ? $mode : var_export($mode, true)
                            )
                        );
                }

                return $run;
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function boot()
    {
        container($this->serviceName)->register();
    }
}
