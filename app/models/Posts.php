<?php

namespace Forum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Posts extends Model
{

	public $id;

	public $users_id;

	public $categories_id;

	public $title;

	public $slug;

	public $content;

	public function beforeValidationOnCreate()
	{
		$this->number_views = 0;
		$this->number_replies = 0;
	}

	public function initialize()
	{
		$this->belongsTo('users_id', 'Forum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->belongsTo('categories_id', 'Forum\Models\Categories', 'id', array(
			'alias' => 'category',
			'foreignKey' => array(
				'message' => 'The category is not valid'
			)
		));

		$this->hasMany('id', 'Forum\Models\PostsReplies', 'posts_id', array(
			'alias' => 'replies'
		));

		$this->hasMany('id', 'Forum\Models\PostsViews', 'posts_id', array(
			'alias' => 'views'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			)
		)));
	}

	public function afterCreate()
	{
		if ($this->id > 0) {
			$activity = new Activities();
			$activity->users_id = $this->users_id;
			$activity->posts_id = $this->id;
			$activity->type = 'P';
			$activity->save();
		}
	}

}