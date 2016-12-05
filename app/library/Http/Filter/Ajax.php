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

namespace Phosphorum\Http\Filter;

use Phalcon\Mvc\User\Component;
use Phosphorum\Http\FilterInterface;

/**
 * AJAX HTTP filter
 *
 * @package Phosphorum\Http\Filter
 */
class Ajax extends Component implements FilterInterface
{
    /**
     * Check if the request was made with Ajax
     *
     * @return bool
     */
    public function check()
    {
        $request = $this->di->getShared('request');

        return $request->isAjax();
    }
}
