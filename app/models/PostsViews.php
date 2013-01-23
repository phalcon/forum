<?php

namespace Forum\Models;

use Phalcon\Mvc\Model;

class PostsViews extends Model
{

	public $id;

	public $posts_id;

	public $ipaddress;

	public function initialize()
	{
		$this->belongsTo('posts_id', 'Forum\Models\Posts', 'id', array(
			'alias' => 'post'
		));
	}
}