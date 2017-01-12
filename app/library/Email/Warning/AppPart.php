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

namespace Phosphorum\Email\Warning;

use Egulias\EmailValidator\Warning\Warning;

/**
 * Phosphorum\Email\Warning\AppPart
 *
 * @package Phosphorum\Email\Warning
 */
class AppPart extends Warning
{
    const CODE = 10001;

    public function __construct()
    {
        $this->message = 'The corporate address is not intended for mailing';
    }
}
