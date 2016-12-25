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

namespace Phosphorum\Provider\Dispatcher;

use InvalidArgumentException;
use Phalcon\Mvc\Dispatcher as MvcDi;
use Phalcon\Cli\Dispatcher as CliDi;
use Phosphorum\Listener\DispatcherListener;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Dispatcher\ServiceProvider
 *
 * @package Phosphorum\Provider\Dispatcher
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'dispatcher';

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
                        $dispatcher = new MvcDi();
                        $dispatcher->setDefaultNamespace('Phosphorum\Controller');

                        container('eventsManager')->attach('dispatch', new DispatcherListener(container()));

                        break;
                    case 'cli':
                        $dispatcher = new CliDi();
                        $dispatcher->setDefaultNamespace('Phosphorum\Task');

                        $dispatcher->setActionSuffix('');
                        $dispatcher->setTaskSuffix('');
                        $dispatcher->setDefaultTask('help');

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

                $dispatcher->setDI(container());
                $dispatcher->setEventsManager(container('eventsManager'));

                return $dispatcher;
            }
        );
    }
}
