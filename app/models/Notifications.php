<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Notifications extends Model
{

	public $id;

	public $users_id;

	public $type;

	public $posts_id;

	public $posts_replies_id;

	public $created_at;

	public $modified_at;

	public $message_id;

	public $sent;

	public function beforeValidationOnCreate()
	{
		$this->sent = 'N';
	}

	public function initialize()
	{
		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post'
		));

		$this->belongsTo('posts_replies_id', 'Phosphorum\Models\PostsReplies', 'id', array(
			'alias' => 'reply'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			),
			'beforeUpdate' => array(
				'field' => 'modified_at'
			)
		)));
	}

}