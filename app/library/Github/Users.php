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

    protected $response;

    /**
     * Users constructor
     *
     * @param string $accessToken
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->response   = $this->request('/user');
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
            return @json_decode(
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
        return is_array($this->response);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (isset($this->response['name'])) {
            if ($this->response['name']) {
                return $this->response['name'];
            }
        }
        return $this->response['login'];
    }

    /**
     * @return null
     */
    public function getEmail()
    {

        if (isset($this->response['email'])) {
            if ($this->response['email'] && strpos($this->response['email'], '@') !== false) {
                return $this->response['email'];
            }
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
        return $this->response['login'];
    }

    /**
     * @return mixed
     */
    public function getGravatarId()
    {
        return $this->response['gravatar_id'];
    }
}
