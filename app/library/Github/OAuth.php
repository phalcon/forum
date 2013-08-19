<?php

namespace Phosphorum\Github;

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
        $key=$this->request->getQuery('statekey');
        $value=$this->request->getQuery('state');


        if( ! $this->di->get("security")->checkToken($key,$value))
            return false;

		$this->view->disable();
		$response = $this->send($this->_endPointAccessToken, array(
			'client_id' => $this->_clientId,
			'client_secret' => $this->_clientSecret,
			'code' => $this->request->getQuery('code'),
			'state' => $this->request->getQuery('state')
		));

		return $response;
	}

	public function send($url, $parameters, $method=\HttpRequest::METH_POST)
	{
		try {

			$transport = $this->getTransport();

			$transport->setHeaders(array(
				'Accept' => 'application/json'
			));

			$transport->setUrl($url);
			$transport->setMethod($method);

			switch ($method) {
				case \HttpRequest::METH_POST:
					$transport->addPostFields($parameters);
					break;
				case \HttpRequest::METH_GET:
					$transport->addQueryData($parameters);
					break;
			}

			$transport->send();

			return json_decode($transport->getResponseBody(), true);

		} catch (\HttpInvalidParamException $e) {
			return false;
		} catch (\HttpRequestException $e) {
			return false;
		}

	}

	public function getTransport()
	{
		if (!$this->_transport) {
			$this->_transport = new \HttpRequest();
		}
		return $this->_transport;
	}

}
