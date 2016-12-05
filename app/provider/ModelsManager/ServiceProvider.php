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

namespace Phosphorum\Provider\ModelsManager;

use Phalcon\Mvc\Model\Manager;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\ModelsManager\ServiceProvider
 *
 * @package Phosphorum\Provider\ModelsManager
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'modelsManager';

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
                $modelsManager = new Manager();
                $modelsManager->setEventsManager(container('eventsManager'));

                return $modelsManager;
            }
        );
    }
}
