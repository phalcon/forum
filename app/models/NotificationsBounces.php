<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;

class NotificationsBounces extends Model
{

	public $id;

	public $email;

	public $status;

	public $diagnostic;

	public $created_at;

	public $reported;

	const MAX_BOUNCES = 3;

	public function beforeValidationOnCreate()
	{
		$this->reported = 'N';
		$this->created_at = time();
	}

}