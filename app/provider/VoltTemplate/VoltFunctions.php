<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Provider\VoltTemplate;

use Phosphorum\Version;

/**
 * Phosphorum\Provider\VoltTemplate\VoltFunctions
 *
 * @package Phosphorum\Provider\VoltTemplate
 */
class VoltFunctions
{
    /**
     * Compile any function call in a template.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return null|string
     */
    public function compileFunction($name, $arguments)
    {
        switch ($name) {
            case 'number_format':
            case 'chr':
                return $name . '(' . $arguments . ')';
            case 'gravatar':
                return 'container("gravatar")->getAvatar(' . $arguments . ')';
            case 'forum_version':
                return Version::class . '::get()';
            case 'forum_name':
                return '"'. container('config')->site->software . '"';
        }

        return null;
    }

    /**
     * Compile some filters.
     *
     * @param  string $name      The filter name
     * @param  mixed  $arguments The filter args
     * @return string|null
     */
    public function compileFilter($name, $arguments)
    {
        switch ($name) {
            // @todo
        }

        return null;
    }
}
