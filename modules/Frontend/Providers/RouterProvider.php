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

namespace Phosphorum\Frontend\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\RouterInterface;
use Phalcon\Mvc\Router\Group;

/**
 * Phosphorum\Frontend\Providers\RouterProvider
 *
 * @package Phosphorum\Frontend\Providers
 */
class RouterProvider implements ServiceProviderInterface
{
    /**
     * Default module name.
     *
     * @var string
     */
    private $moduleName;

    /**
     * Default module name.
     *
     * @var string|null
     */
    private $namespaceName;

    /**
     * RoutingProvider constructor.
     *
     * @param string $moduleName
     * @param string $namespaceName
     */
    public function __construct(string $moduleName, string $namespaceName)
    {
        $this->setModuleName($moduleName);
        $this->setDefaultNamespace($namespaceName);
    }

    /**
     * Sets the name of the default module.
     *
     * @param string $moduleName
     */
    protected function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * Sets the name of the default namespace.
     *
     * @param string $namespaceName
     */
    protected function setDefaultNamespace(string $namespaceName)
    {
        $this->namespaceName = $namespaceName;
    }

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $discussionsGroup = $this->createDiscussionsGroup($this->moduleName);

        /** @var RouterInterface $router */
        $router = $container->getShared('router');

        $router->setDefaultModule($this->moduleName);
        $router->mount($discussionsGroup);

        if ($router instanceof Router == true) {
            /** @var Router $router */
            $router->setDefaultNamespace($this->namespaceName);
        }
    }

    /**
     * Create the Discussions Routes Group with the specified module.
     *
     * @param  string $moduleName
     * @return Group
     */
    protected function createDiscussionsGroup(string $moduleName): Group
    {
        $discussions = new Group(
            [
                'module' => $moduleName,
            ]
        );

        $discussions->add(
            '/',
            [
                'controller' => 'discussions',
                'action'     => 'index',
            ]
        );

        return $discussions;
    }
}
