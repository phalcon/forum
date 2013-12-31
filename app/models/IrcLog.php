<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class IrcLog extends Model
{

	public $id;

	public $who;

	public $content;

	public $datelog;

	public function initialize()
	{
		$this->setSource('irclog');
	}

}