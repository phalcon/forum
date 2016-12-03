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

namespace Phosphorum\Providers\Flash;

use Phalcon\Flash\Direct;
use Phalcon\Flash\Session;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\Flash\ServiceProvider
 *
 * @package Phosphorum\Providers\Flash
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'flash';

    protected $bannerStyle = [
        'error'   => 'alert alert-danger fade in',
        'success' => 'alert alert-success fade in',
        'notice'  => 'alert alert-info fade in',
        'warning' => 'alert alert-warning fade in',
    ];

    /**
     * {@inheritdoc}
     *
     * Register the Flash Service with the Twitter Bootstrap classes.
     *
     * @return void
     */
    public function register()
    {
        $bannerStyle = $this->bannerStyle;

        $this->di->set(
            $this->serviceName,
            function () use ($bannerStyle) {
                $flash = new Direct($bannerStyle);

                $flash->setAutoescape(true);
                $flash->setDI(container());
                $flash->setCssClasses($bannerStyle);

                return $flash;
            }
        );

        $this->di->setShared(
            'flashSession',
            function () use ($bannerStyle) {
                $flash = new Session($bannerStyle);

                $flash->setAutoescape(true);
                $flash->setDI(container());
                $flash->setCssClasses($bannerStyle);

                return $flash;
            }
        );
    }
}
