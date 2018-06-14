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

/**
 * Phosphorum\Core\Modules\ManagerInterface
 *
 * @package Phosphorum\Core\Modules
 */
interface ManagerInterface
{
    /**
     * Register modules present in the application.
     *
     * @param  Application $application
     * @param  bool        $merge
     *
     * @return void
     */
    public function registerModules(Application $application, bool $merge = true): void;
}
