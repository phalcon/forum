<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class PostsBounties extends Model
{

	public $id;

	public $posts_id;

	public $users_id;

	public $posts_replies_id;

	public $points;

	public $created_at;

	public function initialize()
	{
		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post',
			'reusable' => true
		));

		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user',
			'reusable' => true
		));

		$this->addBehavior(new Timestampable(array(
			'beforeValidationOnCreate' => array(
				'field' => 'created_at'
			)
		)));
	}

}