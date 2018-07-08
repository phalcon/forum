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

use Closure;
use Phosphorum\Core\Paginator\PaginatorManager;
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

/**
 * Phosphorum\Core\Providers\PaginatorProvider
 *
 * @package Phosphorum\Core\Providers
 */
class PaginatorProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared(PaginatorManager::class, $service);
    }

    protected function createService(DiInterface $container): Closure
    {
        return function () use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            $paginatorConfig = $config->get('paginator');
            if ($paginatorConfig instanceof Config == false) {
                $paginatorConfig = new Config();
            }

            $manager = new PaginatorManager(
                $paginatorConfig,
                $container
            );

            return $manager;
        };
    }
}
