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
 * Phosphorum\Email\Vendor\Yahoo
 *
 * @package Phosphorum\Email\Vendor
 */
class Yahoo extends AbstractVendor
{
    const ID    = 12;
    const NAME  = 'Yahoo!';

    // @codingStandardsIgnoreStart
    const REGEXP = '@((yhoo|ymail)\.com|((qc|ca)?\.yahoo\.com)|yahoo\.(com(\.(ar|au|br|co|hr|hk|my|mx|ph|sg|tw|tr|vn))?|ae|at|ch|es|fr|be|co\.(in|id|il|jp|nz|za|th|uk)|cz|dk|fi|de|gr|hu|in|ie|it|nl|no|pl|pt|ro|ru|se))';
    // @codingStandardsIgnoreEnd
}
