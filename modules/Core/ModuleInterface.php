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

use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Phosphorum\Core\Modules\ModuleInterface
 *
 * @package Phosphorum\Core\Modules
 */
interface ModuleInterface extends ModuleDefinitionInterface
{
    /**
     * Get module name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get module path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function getPath(string $path = ''): string;

    /**
     * Gets the name of the default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace(): string;

    /**
     * Get service registrator.
     *
     * @return ServiceRegistrator
     */
    public function getServiceRegistrator(): ServiceRegistrator;
}
