<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class PostsVotes extends Model
{

	public $id;

	public $posts_id;

	public $users_id;

	public $created_at;

	public function initialize()
	{
		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post'
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
			$viewCache = $this->getDI()->getViewCache();
			$viewCache->delete('post-' . $this->posts_id);
		}
	}

}