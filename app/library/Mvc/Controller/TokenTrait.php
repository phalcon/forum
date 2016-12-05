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

namespace Phosphorum\Mvc\Controller;

/**
 * Token Trait
 *
 * @package Phosphorum\Mvc\Controller
 *
 * @property \Phosphorum\Provider\Security\Security $security
 * @property \Phalcon\FlashInterface $flashSession
 * @property \Phalcon\Session\AdapterInterface $session
 * @property \Phalcon\Http\RequestInterface $request
 */
trait TokenTrait
{
    protected $csrfSessionKey = '$PHALCON/CSRF/KEY$';
    protected $csrfErrorMessage = 'This form has altered. Please try submitting it again.';

    protected function checkTokenPost($prefix = null)
    {
        if ($prefix) {
            $result = $this->security->checkPrefixedToken($prefix);
        } else {
            $result = $this->security->checkToken();
        }

        if (!$result) {
            $this->flashSession->error($this->csrfErrorMessage);
            return false;
        }

        return true;
    }

    protected function checkTokenGetJson($prefix = null)
    {
        if ($prefix) {
            $csrfKey = $this->session->get($prefix . ':' . $this->csrfSessionKey);
            $csrfVal = $this->request->getQuery($csrfKey, null, '');

            return $this->security->checkPrefixedToken($prefix, $csrfKey, $csrfVal);
        }

        $csrfKey = $this->session->get($this->csrfSessionKey);
        $csrfVal = $this->request->getQuery($csrfKey, null, '');

        return $this->security->checkToken($csrfKey, $csrfVal);
    }

    protected function checkTokenGet($prefix = null)
    {
        if ($prefix) {
            $csrfKey = $this->session->get($prefix . ':' . $this->csrfSessionKey);
            $csrfVal = $this->request->getQuery($csrfKey, null, '');

            if (!$this->security->checkPrefixedToken($prefix, $csrfKey, $csrfVal)) {
                $this->flashSession->error($this->csrfErrorMessage);
                return false;
            }
        } else {
            $csrfKey = $this->session->get($this->csrfSessionKey);
            $csrfVal = $this->request->getQuery($csrfKey, null, '');

            if (!$this->security->checkToken($csrfKey, $csrfVal)) {
                $this->flashSession->error($this->csrfErrorMessage);
                return false;
            }
        }

        return true;
    }
}
