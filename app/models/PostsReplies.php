<?php

namespace Forum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class PostsReplies extends Model
{
	public function initialize()
	{
		$this->belongsTo('posts_id', 'Forum\Models\Posts', 'id', array(
			'alias' => 'post'
		));

		$this->belongsTo('users_id', 'Forum\Models\Users', 'id', array(
			'alias' => 'user'
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

	public function afterCreate()
	{
		if ($this->id > 0) {
			$activity = new Activities();
			$activity->users_id = $this->users_id;
			$activity->posts_id = $this->posts_id;
			$activity->type = 'C';
			$activity->save();
		}
	}
}