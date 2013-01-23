<?php

namespace Forum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Activities extends Model
{

	public function initialize()
	{
		$this->belongsTo('users_id', 'Forum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->belongsTo('posts_id', 'Forum\Models\Posts', 'id', array(
			'alias' => 'post'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			)
        )));
	}

}