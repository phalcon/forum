<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;

class PostsNotifications extends Model
{

	public $id;

	public $posts_id;

	public $users_id;

	public function initialize()
	{
		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post'
		));
	}
}