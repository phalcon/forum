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

namespace Phosphorum\Provider;

use Phalcon\Di\InjectionAwareInterface;

/**
 * Phosphorum\Provider\ServiceProviderInterface
 *
 * @package Phosphorum\Provider
 */
interface ServiceProviderInterface extends InjectionAwareInterface
{
    /**
     * Register application service.
     *
     * @return void
     */
    public function register();

    /**
     * Package boot method.
     *
     * @return void
     */
    public function boot();

    /**
     * Configures the current service provider.
     *
     * @return void
     */
    public function configure();
    /**
     * Get the Service name.
     *
     * @return string
     */
    public function getName();
}
