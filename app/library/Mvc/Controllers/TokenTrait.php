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
 * @property \Phalcon\FlashInterface $flashSession
 * @property \Phalcon\Session\AdapterInterface $session
 * @property \Phalcon\Http\RequestInterface $request
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

    protected function checkTokenGetJson()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');

        return $this->security->checkToken(
            $csrfKey,
            $this->request->getQuery($csrfKey, null, 'dummy')
        );
    }

    protected function checkTokenGet()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');

        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }
        return true;
    }
}
