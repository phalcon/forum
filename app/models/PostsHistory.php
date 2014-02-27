<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;

class PostsHistory extends Model
{

	public $id;

	public $posts_id;

	public $users_id;

	public $content;

	public $created_at;

	public function beforeValidationOnCreate()
	{
		$this->created_at = time();
	}

	public function initialize()
	{
		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post'
		));
	}
}