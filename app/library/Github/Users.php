<?php

namespace Forum\Github;

class Users
{

	protected $_endPoint = 'https://api.github.com/user';

	public function __construct($accessToken)
	{
		try {
			$transport = new \HttpRequest($this->_endPoint.'?access_token='.$accessToken);
			$transport->send();
			$this->_response = json_decode($transport->getResponseBody(), true);
		} catch (\HttpInvalidParamException $e) {
			$this->_response = null;
		}
	}

	public function isValid()
	{
		return is_array($this->_response);
	}

	public function getName()
	{
		return $this->_response['name'];
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