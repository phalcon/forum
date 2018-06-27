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
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phosphorum\Core\Cache\CacheManager;

/**
 * Phosphorum\Core\Providers\ModelsCacheProvider
 *
 * @package Phosphorum\Core\Providers
 */
class ModelsCacheProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared('modelsCache', $service);
    }

    protected function createService(DiInterface $container): Closure
    {
        return function () use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            $cacheConfig = $config->get('cache');
            if ($cacheConfig instanceof Config == false) {
                $cacheConfig = new Config();
            }

            $manager = new CacheManager();

            return $manager->create($cacheConfig);
        };
    }
}
