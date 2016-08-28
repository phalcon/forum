<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Utils;

use Phalcon\Security as PhSecurity;

/***
 * Phosphorum\Utils\Security
 *
 * This component provides a set of functions to improve the security in Forum application.
 * Prefixed version.
 *
 * <code>
 * $login = $this->request->getPost('login');
 * $password = $this->request->getPost('password');
 *
 * $user = Users::findFirstByLogin($login);
 * if ($user && $this->security->checkHash($password, $user->password)) {
 *     //The password is valid
 * }
 * </code>
 *
 * @package Phosphorum\Utils
 * @property \Phalcon\Security\Random $_random
 */
class Security extends PhSecurity
{
    private $prefixedTokenKeys = [];
    private $prefixedTokens    = [];

    /**
     * Generates a pseudo random token key to be used as input's name in a CSRF check.
     *
     * @param string $prefix
     * @return string
     */
    public function getPrefixedTokenKey($prefix)
    {
        $key = $prefix . ':' . $this->_tokenKeySessionID;

        if (!isset($this->prefixedTokenKeys[$key])) {
            $tokenKey = $this->_random->base64Safe($this->_numberBytes);

            $this->prefixedTokenKeys[$key] = $tokenKey;
            /** @var \Phalcon\Session\AdapterInterface $session */
            $session = $this->getDI()->getShared('session');
            $session->set($key, $tokenKey);
        }

        return $this->prefixedTokenKeys[$key];
    }

    /**
     * Generates a pseudo random token value to be used as input's value in a CSRF check.
     *
     * @param string $prefix
     * @return string mixed
     */
    public function getPrefixedToken($prefix)
    {
        $key = $prefix . ':' . $this->_tokenValueSessionID;

        if (!isset($this->prefixedTokens[$key])) {
            $token = $this->_random->base64Safe($this->_numberBytes);

            $this->prefixedTokens[$key] = $token;
            /** @var \Phalcon\Session\AdapterInterface $session */
            $session = $this->getDI()->getShared('session');
            $session->set($key, $token);
        }

        return $this->prefixedTokens[$key];
    }

    /**
     * Check if the CSRF token sent in the request is the same that the current in session.
     *
     * @param string $prefix
     * @param string $tokenKey
     * @param string $tokenValue
     * @param bool $destroyIfValid
     * @return bool
     */
    public function checkPrefixedToken($prefix, $tokenKey = null, $tokenValue = null, $destroyIfValid = true)
    {
        $prefixedKey = $prefix . ':' . $this->_tokenKeySessionID;
        $prefixedValue = $prefix . ':' . $this->_tokenValueSessionID;

        /** @var \Phalcon\Session\AdapterInterface $session */
        $session = $this->getDI()->getShared('session');

        if (!$tokenKey) {
            $tokenKey = $session->get($prefixedKey);
        }

        if (!$tokenKey) {
            return false;
        }

        if (!$tokenValue) {
            /** @var \Phalcon\Http\Request $request */
            $request = $this->getDI()->getShared('request');
            $tokenValue = $request->getPost($tokenKey);
        }

        $returnValue = ($tokenValue == $session->get($prefixedValue));

        if ($returnValue && $destroyIfValid) {
            $this->destroyPrefixedToken($prefix);
        }

        return $returnValue;
    }

    /**
     * Returns the value of the CSRF token in session.
     *
     * @param string $prefix
     * @return mixed
     */
    public function getPrefixedSessionToken($prefix)
    {
        $prefixedValue = $prefix . ':' . $this->_tokenValueSessionID;

        /** @var \Phalcon\Session\AdapterInterface $session */
        $session = $this->getDI()->getShared('session');
        return $session->get($prefixedValue);
    }

    /**
     * Removes the value of the CSRF token and key from session.
     *
     * @param string $prefix
     * @return $this
     */
    public function destroyPrefixedToken($prefix)
    {
        $prefixedKey = $prefix . ':' . $this->_tokenKeySessionID;
        $prefixedValue = $prefix . ':' . $this->_tokenValueSessionID;

        /** @var \Phalcon\Session\AdapterInterface $session */
        $session = $this->getDI()->getShared('session');

        $session->remove($prefixedKey);
        $session->remove($prefixedValue);

        unset($this->prefixedTokenKeys[$prefixedKey], $this->prefixedTokens[$prefixedValue]);

        return $this;
    }
}
