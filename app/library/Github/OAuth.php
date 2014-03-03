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

class OAuth extends \Phalcon\DI\Injectable
{

	protected $_endPointAuthorize = 'https://github.com/login/oauth/authorize';

	protected $_endPointAccessToken = 'https://github.com/login/oauth/access_token';

	protected $_redirectUriAuthorize;

	protected $_baseUri;

	protected $_clientId;

	protected $_clientSecret;

	protected $_transport;

	public function __construct($config)
	{
		$this->_redirectUriAuthorize = $config->redirectUri;
		$this->_clientId = $config->clientId;
		$this->_clientSecret = $config->clientSecret;
	}

	public function authorize()
	{
		$this->view->disable();

        $key = $this->security->getTokenKey();
        $token = $this->security->getToken();

		$url = $this->_endPointAuthorize.
			'?client_id='.$this->_clientId.
			'&redirect_uri='.$this->_redirectUriAuthorize.
                urlencode('&statekey=' .$key) . // add the tokenkey as a query param. Then we will be able to use it to check token authenticity
			'&state='.$token.
			'&scope=user:email';
		$this->response->redirect($url, true);
	}

	public function accessToken()
	{

        // check the securtity - anti csrf token
        $key = $this->request->getQuery('statekey');
        $value = $this->request->getQuery('state');

        if (!$this->di["security"]->checkToken($key, $value)) {
            return false;
		}

		$this->view->disable();
		$response = $this->send($this->_endPointAccessToken, array(
			'client_id' => $this->_clientId,
			'client_secret' => $this->_clientSecret,
			'code' => $this->request->getQuery('code'),
			'state' => $this->request->getQuery('state')
		));

		return $response;
	}

	public function send($url, $parameters, $method='post')
	{
		try {

			$client = new HttpClient();

			$headers = array(
				'Accept' => 'application/json'
			);

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

			return json_decode((string) $request->send()->getBody(), true);

		} catch (\Exception $e) {
			//file_put_contents('error.txt', $e->getMessage());
			return false;
		}

	}

}
