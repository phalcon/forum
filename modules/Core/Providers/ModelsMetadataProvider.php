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
use Phosphorum\Core\Environment;
use Phosphorum\Core\Models\MetaDataManager;

/**
 * Phosphorum\Core\Providers\ModelsMetadataProvider
 *
 * @package Phosphorum\Core\Providers
 */
class ModelsMetadataProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared('modelsMetadata', $service);
    }

    protected function createService(DiInterface $container): Closure
    {
        return function () use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            $metadataConfig = $config->get('metadata');
            if ($metadataConfig instanceof Config == false) {
                $metadataConfig = new Config();
            }

            $manager = new MetaDataManager();
            $modelsMetadata = $manager->create(
                $container->get(Environment::class),
                $metadataConfig
            );

            return $modelsMetadata;
        };
    }
}
