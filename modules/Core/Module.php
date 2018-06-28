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

namespace Phosphorum\Core;

use Phalcon\DiInterface;
use Phalcon\Escaper;
use Phalcon\Events\ManagerInterface;
use Phalcon\Filter;
use Phalcon\Http\Response;
use Phalcon\Tag;
use Phosphorum\Core\Modules\AbstractModule;
use Phosphorum\Core\Providers;

/**
 * Phosphorum\Core\Module
 *
 * @package Phosphorum\Core
 */
class Module extends AbstractModule
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface      $container
     * @param ManagerInterface $eventManager
     */
    public function __construct(DiInterface $container, ManagerInterface $eventManager)
    {
        parent::__construct($container, $eventManager);

        $this->registerBaseBindings($container);
        $this->registerBaseServices();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Core';
    }

    /**
     * Register base services.
     *
     * @return void
     */
    protected function registerBaseServices(): void
    {
        $this->serviceRegistrator->registerService(new Providers\ConfigProvider());
        $this->serviceRegistrator->registerService(new Providers\LoggerProvider());

        $this->serviceRegistrator->registerService(new Providers\VoltProvider());
        $this->serviceRegistrator->registerService(new Providers\ViewProvider());

        $this->serviceRegistrator->registerService(new Providers\UrlResolverProvider());
        $this->serviceRegistrator->registerService(new Providers\DispatcherProvider());

        $this->serviceRegistrator->registerService(new Providers\SessionProvider());
        $this->serviceRegistrator->registerService(new Providers\AssetsManagerProvider());

        $this->serviceRegistrator->registerService(new Providers\DatabaseProvider());

        $this->serviceRegistrator->registerService(new Providers\ModelsManagerProvider());
        $this->serviceRegistrator->registerService(new Providers\ModelsMetadataProvider());
        $this->serviceRegistrator->registerService(new Providers\ModelsCacheProvider());
    }

    /**
     * Registers the base bindings.
     *
     * @param  DiInterface $container
     * @return void
     */
    protected function registerBaseBindings(DiInterface $container): void
    {
        $container->setShared('response', Response::class);
        $container->setShared('filter', Filter::class);
        $container->setShared('tag', Tag::class);
        $container->setShared('escaper', Escaper::class);
    }
}
