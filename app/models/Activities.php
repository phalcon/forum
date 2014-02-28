<?php

namespace Phosphorum\Models;

use Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Activities extends Model
{

	public $id;

	public $users_id;

	public $type;

	public $posts_id;

	public $created_at;

	public function initialize()
	{
		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user',
			'reusable' => true
		));

		$this->belongsTo('posts_id', 'Phosphorum\Models\Posts', 'id', array(
			'alias' => 'post',
			'reusable' => true
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			)
		)));
	}

	public function getHumanCreatedAt()
	{
		$diff = time() - $this->created_at;
		if ($diff > (86400 * 30)) {
			return date('M \'y', $this->created_at);
		} else {
			if ($diff > 86400) {
				return ((int) ($diff / 86400)) . 'd';
			} else {
				if ($diff > 3600) {
					return ((int) ($diff / 3600)) . 'h';
				} else {
					return ((int) ($diff / 60)) . 'm';
				}
			}
		}
	}

}