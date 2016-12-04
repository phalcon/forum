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

namespace Phosphorum\Github;

use Guzzle\Http\Client as HttpClient;
use Phalcon\Di\Injectable;

/**
 * Class Users
 *
 * @package Phosphorum\Github
 */
class Users extends Injectable
{

    protected $endPoint = 'https://api.github.com';

    protected $accessToken;

    protected $response;

    protected $logger;

    /**
     * Users constructor
     *
     * @param string $accessToken
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->logger      = $this->getDI()->get('logger', ['auth']);
        $this->response    = $this->request('/user');
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
            $url    = sprintf(
                '%s%s?access_token=%s',
                $this->endPoint,
                $method,
                $this->accessToken
            );
            return @json_decode(
                $client->get($url)->send()->getBody(),
                true
            );
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Invalid GitHub response for token: %s. %s',
                    $this->accessToken,
                    $e->getMessage()
                )
            );
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
        if (isset($this->response['name']) && $this->response['name']) {
            return $this->response['name'];
        }

        return $this->response['login'];
    }

    /**
     * @return null
     */
    public function getEmail()
    {
        if (isset($this->response['email']) && false !== strpos($this->response['email'], '@')) {
            return $this->response['email'];
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
