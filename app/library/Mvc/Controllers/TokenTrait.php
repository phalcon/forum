<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2015 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Mvc\Controllers;

/**
 * Token Trait
 *
 * @package Phosphorum\Mvc\Controllers
 *
 * @property \Phalcon\Security $security
 * @property \Phalcon\Flash\Session $flashSession
 */
trait TokenTrait
{
    protected function checkTokenPost()
    {
        if (!$this->security->checkToken()) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }

        return true;
    }
}
