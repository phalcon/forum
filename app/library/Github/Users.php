<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Github;

use Guzzle\Http\Client as HttpClient;

/**
 * Class Users
 *
 * @package Phosphorum\Github
 */
class Users
{

    protected $endPoint = 'https://api.github.com';

    protected $accessToken;

    /**
     * @param $accessToken
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->_response    = $this->request('/user');
    }

    /**
     * @param $method
     *
     * @return mixed|null
     */
    public function request($method)
    {
        try {
            $client = new HttpClient();
            return json_decode(
                $client->get($this->endPoint . $method . '?access_token=' . $this->accessToken)->send()->getBody(),
                true
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_array($this->_response);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->_response['name']) {
            return $this->_response['name'];
        }
        return $this->_response['login'];
    }

    /**
     * @return null
     */
    public function getEmail()
    {
        if ($this->_response['email']) {
            return $this->_response['email'];
        }

        $emails = $this->request('/user/emails');
        if (count($emails)) {

            return $emails[0];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->_response['login'];
    }

    /**
     * @return mixed
     */
    public function getGravatarId()
    {
        return $this->_response['gravatar_id'];
    }
}
