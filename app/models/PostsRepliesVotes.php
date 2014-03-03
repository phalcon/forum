<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class PostsRepliesVotes extends Model
{

	public $id;

	public $posts_replies_id;

	public $users_id;

	public $created_at;

	public function initialize()
	{
		$this->belongsTo('posts_replies_id', 'Phosphorum\Models\PostsReplies', 'id', array(
			'alias' => 'postReply'
		));

		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeValidationOnCreate' => array(
				'field' => 'created_at'
			)
        )));
	}

	public function afterSave()
	{
		if ($this->id) {
			$this->postReply->clearCache();
		}
	}

}