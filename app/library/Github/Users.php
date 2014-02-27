<?php

namespace Phosphorum\Github;

use Guzzle\Http\Client as HttpClient;

class Users
{

	protected $_endPoint = 'https://api.github.com';

	protected $_accessToken;

	public function __construct($accessToken)
	{
		$this->_accessToken = $accessToken;
		$this->_response = $this->request('/user');
	}

	public function request($method)
	{
		try {
			$client = new HttpClient();			
			return json_decode($client->get($this->_endPoint . $method . '?access_token=' . $this->_accessToken)->send()->getBody(), true);
		} catch (\Exception $e) {
			return null;
		}
	}

	public function isValid()
	{
		return is_array($this->_response);
	}

	public function getName()
	{
		if ($this->_response['name']) {
			return $this->_response['name'];
		}
		return $this->_response['login'];
	}

	public function getEmail()
	{
		if ($this->_response['email']) {
			return $this->_response['email'];
		}

		$emails = $this->request('/user/emails');
		if (count($emails)){
			return $emails[0];
		}

		return null;
	}

	public function getLogin()
	{
		return $this->_response['login'];
	}

	public function getGravatarId()
	{
		return $this->_response['gravatar_id'];
	}

}