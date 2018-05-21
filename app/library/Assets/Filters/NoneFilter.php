<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Assets\Filters;

use Phalcon\Assets\FilterInterface;

/**
 * Phosphorum\Assets\Filters\NoneFilter
 * @package Phosphorum\Assets\Filters
 */
class NoneFilter implements FilterInterface
{
    /**
     * @param string $contents
     * @return string
     */
    public function filter($contents)
    {
        return (string) $contents;
    }
}
