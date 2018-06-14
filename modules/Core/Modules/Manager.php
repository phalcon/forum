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

namespace Phosphorum\Core\Modules;

use Phalcon\Application;
use Phalcon\DiInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Registry;
use Phosphorum\Core\ModuleInterface;
use Phosphorum\Core\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Modules\Manager
 *
 * @package Phosphorum\Core\Modules
 */
final class Manager implements ManagerInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /**
     * A list of the ready to use modules.
     *
     * @var array
     */
    protected $modules = [];

    /**
     * Internal application registry.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Internal application events manager.
     *
     * @var EventsManagerInterface
     */
    protected $eventsManager;

    /**
     * ModulesRegistrator constructor.
     *
     * @param DiInterface|null $container
     */
    public function __construct(DiInterface $container = null)
    {
        $this->__DiInject($container);

        $this->registry = $this->getDI()->get(Registry::class);
        $this->eventsManager = $this->getDI()->get('eventsManager');
    }

    /**
     * Initialize modules and register them in the internal stack.
     *
     * @return void
     */
    protected function initializeModules(): void
    {
        $container = $this->getDI();
        $modules = $this->lookupModules();

        foreach ($modules as $moduleName => $moduleClass) {
            if (isset($this->modules[$moduleName])) {
                continue;
            }

            /** @var ModuleInterface $module */
            $module = new $moduleClass($container, $this->eventsManager);

            // Do not register anything else
            if ($module instanceof ModuleInterface == false) {
                continue;
            }

            $this->modules[$moduleName] = [
                'path'      => $module->getPath(),
                'className' => get_class($module),
            ];

            $container->setShared(get_class($module), $module);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param  Application $application
     * @param  bool        $merge
     *
     * @return void
     */
    public function registerModules(Application $application, bool $merge = true): void
    {
        $this->initializeModules();

        $application->registerModules(
            $this->modules,
            $merge
        );
    }

    /**
     * Lookup all existent modules.
     *
     * @return array
     */
    protected function lookupModules()
    {
        $modules = [];

        $this->registerBaseModules();

        $this->eventsManager->fire('modules:beforeLookupModules', $this);

        foreach ($this->registry->offsetGet('modules') as $name => $fqmn) {
            $modules[$name] = $fqmn;
        }

        $this->eventsManager->fire('modules:afterLookupModules', $this, $modules);

        return $modules;
    }

    /**
     * Registers the base modules in the internal registry.
     *
     * @return void
     */
    protected function registerBaseModules(): void
    {
        $this->registry->offsetSet('modules', [
            'Core'     => 'Phosphorum\Core\Module',
            'Frontend' => 'Phosphorum\Frontend\Module',
        ]);
    }
}
