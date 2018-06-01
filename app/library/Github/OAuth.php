<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
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
use GuzzleHttp\Client as HttpClient;
use Phosphorum\Exception\InvalidParameterException;

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
     * @param string $url
     * @param array $parameters
     * @param string $method
     *
     * @return bool|mixed
     */
    public function send($url, array $parameters, $method = 'post')
    {
        try {
            if (!in_array($method, ['post', 'get'])) {
                throw new InvalidParameterException('Invalid HTTP method ' . $method);
            }

            $response = (new HttpClient())->request($method, $url, [
                'form_params' => $parameters,
                'headers' => ['Accept' => 'application/json'],
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }
}
