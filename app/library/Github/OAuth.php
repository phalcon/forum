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

use Phalcon\Text;
use Phalcon\Config;
use Phalcon\Di\Injectable;
use Guzzle\Http\Client as HttpClient;

/**
 * Class OAuth
 *
 * @package Phosphorum\Github
 */
class OAuth extends Injectable
{
    protected $endPointAuthorize = 'https://github.com/login/oauth/authorize';

    protected $endPointAccessToken = 'https://github.com/login/oauth/access_token';

    protected $redirectUriAuthorize;

    protected $clientId;

    protected $clientSecret;

    protected $logger;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->redirectUriAuthorize = $config->get('redirectUri');
        $this->clientId             = $config->get('clientId');
        $this->clientSecret         = $config->get('clientSecret');
        $this->logger               = $this->getDI()->get('logger', ['auth']);
    }

    public function authorize()
    {
        $this->view->disable();

        $params = [
            'client_id'    => $this->clientId,
            'redirect_uri' => $this->redirectUriAuthorize . urlencode('&statekey=' . $this->security->getTokenKey()),
            'state'        => $this->security->getToken(),
            'scope'        => 'user:email'
        ];
        $url = $this->endPointAuthorize . '?' . http_build_query($params);

        $this->response->redirect($url, true);
    }

    /**
     * @return bool|mixed
     */
    public function accessToken()
    {
        // check the security - anti csrf token
        $key   = $this->request->getQuery('statekey');
        $value = $this->request->getQuery('state');

        if (!$this->security->checkToken($key, $value)) {
            return false;
        }

        $this->view->disable();

        if ($error = $this->request->getQuery('error')) {
            $error = Text::humanize($error);
            $uri   = $this->request->getQuery('error_uri');
            $this->logger->error("{$error}: " . $this->request->getQuery('error_description') . " (see: {$uri})");

            return false;
        }

        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $this->request->getQuery('code'),
            'state'         => $this->request->getQuery('state')
        ];

        $response = $this->send($this->endPointAccessToken, $params);

        return $response;
    }

    /**
     * @param        $url
     * @param        $parameters
     * @param string $method
     *
     * @return bool|mixed
     */
    public function send($url, $parameters, $method = 'post')
    {
        try {
            $client = new HttpClient();
            $headers = ['Accept' => 'application/json'];

            switch ($method) {
                case 'post':
                    $request = $client->post($url, $headers, $parameters);
                    break;
                case 'get':
                    $request = $client->get($url, $headers, $parameters);
                    break;
                default:
                    throw new \Exception('Invalid HTTP method');
            }

            return json_decode((string)$request->send()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }
}
