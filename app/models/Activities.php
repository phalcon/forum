<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Activities extends Model
{

	public $id;

	public $users_id;

	public $type;

	public $posts_id;

	public $created_at;

	public function initialize()
	{
		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			)
		)));
	}

}