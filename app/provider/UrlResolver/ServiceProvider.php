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

namespace Phosphorum\Provider\UrlResolver;

use Phalcon\Mvc\Url;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\UrlResolver\ServiceProvider
 *
 * @package Phosphorum\Provider\UrlResolver
 */
class ServiceProvider extends AbstractServiceProvider
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

                if (!empty($config->application->staticBaseUri)) {
                    $url->setStaticBaseUri($config->application->staticBaseUri);
                } else {
                    $url->setStaticBaseUri('/');
                }

                if (!empty($config->application->baseUri)) {
                    $url->setBaseUri($config->application->baseUri);
                } else {
                    $url->setBaseUri('/');
                }

                return $url;
            }
        );

        $this->di->setShared('slug', ['className' => Slug::class]);
    }
}
