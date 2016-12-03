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

namespace Phosphorum\Providers\Notification;

use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\Notification\ServiceProvider
 *
 * @package Phosphorum\Providers\Notification
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'notifications';

    /**
     * {@inheritdoc}
     *
     * Initialize the real-time notifications checker.
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared($this->serviceName, Checker::class);
    }
}
