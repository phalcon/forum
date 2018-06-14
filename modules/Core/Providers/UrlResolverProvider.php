<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Url;
use Phalcon\Config;

/**
 * Phosphorum\Core\Providers\UrlResolverProvider
 *
 * @package Phosphorum\Core\Providers
 */
class UrlResolverProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared('url', $service);
    }

    protected function createService(DiInterface $container)
    {
        return function () use ($container) {
            $url = new Url();

            /** @var Config $config */
            $config = $container->get(Config::class);

            $url->setBaseUri($config->path('application.baseUri', '/'));
            $url->setStaticBaseUri($config->path('application.staticBaseUri', '/'));

            return $url;
        };
    }
}
