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

namespace Phosphorum\Email\Vendor;

/**
 * Phosphorum\Email\Vendor\Msn
 *
 * @package Phosphorum\Email\Vendor
 */
class Msn extends AbstractVendor
{
    const ID     = 6;
    const NAME   = 'MSN';
    const REGEXP = '(@(hotmail|windowslive|live|msn|outlook)\.(com|fr|co\.uk))|(@live\.ru)';
}
