<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model;

class PostsRepliesHistory extends Model
{

	public $id;

	public $posts_replies_id;

	public $users_id;

	public $content;

	public $created_at;

	public function beforeValidationOnCreate()
	{
		$this->created_at = time();
	}

	public function initialize()
	{
		$this->belongsTo('posts_replies_id', 'Phosphorum\Models\PostsReplies', 'id', array(
			'alias' => 'postReply'
		));
	}
}