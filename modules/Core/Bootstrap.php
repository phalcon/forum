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

use Phalcon\Cli\Console as PhConsole;
use Phalcon\Mvc\Application as PhApplication;
use Phosphorum\Core\Modules\ManagerInterface;
use Phosphorum\Core\Providers\ConsoleApplicationProvider;
use Phosphorum\Core\Providers\MvcApplicationProvider;

/**
 * Phosphorum\Core\Bootstrap
 *
 * @package Phosphorum\Core
 */
final class Bootstrap
{
    /**
     * The Service Registrator instance.
     *
     * @var ServiceRegistrator
     */
    protected $serviceRegistrator;

    /**
     * The Modules Registrator instance.
     *
     * @var ManagerInterface
     */
    protected $modulesManager;

    /**
     * Bootstrap constructor.
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->serviceRegistrator = new ServiceRegistrator($basePath);
        $this->modulesManager = $this->serviceRegistrator->getDI()->get(ManagerInterface::class);

        $this->registerMvcInternalApplication();
        $this->registerConsoleInternalApplication();
    }

    /**
     * Register Mvc application.
     *
     * @return void
     */
    protected function registerMvcInternalApplication(): void
    {
        $this->serviceRegistrator->registerService(
            new MvcApplicationProvider()
        );
    }

    /**
     * Gets Mvc application.
     *
     * @return PhApplication
     */
    public function makeMvcApplication()
    {
        /** @var PhApplication $application */
        $application = $this->serviceRegistrator->getDI()->get(PhApplication::class);
        $this->modulesManager->registerModules($application);

        return $application;
    }

    /**
     * Register the Console Application.
     *
     * @return void
     */
    protected function registerConsoleInternalApplication(): void
    {
        $this->serviceRegistrator->registerService(
            new ConsoleApplicationProvider()
        );
    }

    /**
     * Gets the Console Application.
     *
     * @return PhConsole
     */
    public function makeConsoleApplication()
    {
        /** @var PhConsole $application */
        $application = $this->serviceRegistrator->getDI()->get(PhConsole::class);
        $this->modulesManager->registerModules($application);

        return $application;
    }
}
