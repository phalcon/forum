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

namespace Phosphorum\Email\Exception;

use Phosphorum\Email\EmailComponent;
use Egulias\EmailValidator\Exception\InvalidEmail;

/**
 * Phosphorum\Email\Exception\Length
 *
 * @package Phosphorum\Email\Exception
 */
class Length extends InvalidEmail
{
    const CODE = 10001;

    public function __construct()
    {
        parent::__construct();

        $this->message = sprintf(
            'Email address must not exceed %s characters or be less than %s characters',
            EmailComponent::MIN_LEN,
            EmailComponent::MAX_LEN
        );
    }
}
