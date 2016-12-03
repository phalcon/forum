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

namespace Phosphorum\Providers\UrlResolver;

use Phalcon\Mvc\Url;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\UrlResolver\ServiceProvider
 *
 * @package Phosphorum\Providers\UrlResolver
 */
class ServiceProvider extends Abstrakt
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'url';

    /**
     * {@inheritdoc}
     * The URL component is used to generate all kind of urls in the application.
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config');

                $url = new Url();

                if (isset($config->application->staticBaseUri)) {
                    $url->setStaticBaseUri($config->application->staticBaseUri);
                } else {
                    $url->setStaticBaseUri('/');
                }

                if (isset($config->application->baseUri)) {
                    $url->setBaseUri($config->application->baseUri);
                } else {
                    $url->setBaseUri('/');
                }

                return $url;
            }
        );
    }
}
