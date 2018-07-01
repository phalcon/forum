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

namespace Phosphorum\Core\Mvc\View\Engine;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;
use Phosphorum\Core\Version;

/**
 * Phosphorum\Core\Mvc\View\Engine\VoltFunctions
 *
 * @package Phosphorum\Core\Mvc\View\Engine
 */
class VoltFunctions implements InjectionAwareInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /**
     * Create a new VoltFunctions instance.
     *
     * @param DiInterface|null $container
     */
    public function __construct(DiInterface $container = null)
    {
        $this->__DiInject($container);
    }

    /**
     * Compile any function call in a template.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return string|null
     */
    public function compileFunction(string $name, $arguments)
    {
        switch ($name) {
            case 'join':
                return 'implode(' . $arguments . ')';
            case 'chr':
            case 'number_format':
                return $name . '(' . $arguments . ')';
            case 'gravatar':
                return '$this->getDI()->get("gravatar")->getAvatar(' . $arguments . ')';
            case 'forum_version':
                return 'str_replace(".", "", ' . Version::class . '::get())';
        }

        return null;
    }

    /**
     * Compile some filters.
     *
     * @param  string $name      The filter name.
     * @param  mixed  $arguments The filter arguments.
     *
     * @return string|null
     */
    public function compileFilter(string $name, $arguments)
    {
        switch ($name) {
            // @TODO
        }

        return null;
    }
}
