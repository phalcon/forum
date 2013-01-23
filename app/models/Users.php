<?php

namespace Forum\Models;

use Forum\Models\Activities,
	Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Users extends Model
{

	public $id;

	public $name;

	public $login;

	public $token_type;

	public $access_token;

	public $gravatar_id;

	public $created_at;

	public $modified_at;

	public function initialize()
	{
		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			),
			'beforeUpdate' => array(
				'field' => 'modified_at'
			)
        )));
	}

	public function afterCreate()
	{
		if ($this->id > 0) {
			$activity = new Activities();
			$activity->users_id = $this->id;
			$activity->type = 'U';
			$activity->save();
		}
	}

}