<?php

namespace Phosphorum\Models;

use Phosphorum\Models\Activities,
	Phalcon\Mvc\Model,
	Phalcon\Mvc\Model\Behavior\Timestampable;

class Users extends Model
{

	public $id;

	public $name;

	public $login;

	public $token_type;

	public $access_token;

	public $gravatar_id;

	public $created_at;

	public $modified_at;

	public $notifications;

	public $timezone;

	public $moderator;

	public $karma;

	public $votes;

	public $votes_points;

	public function initialize()
	{
		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			),
			'beforeUpdate' => array(
				'field' => 'modified_at'
			)
        )));
	}

	public function beforeCreate()
	{
		$this->notifications = 'P';
		$this->moderator = 'N';
		$this->karma += 45;
		$this->votes_points += 45;
		$this->votes = 0;
		$this->timezone = 'Europe/London';
	}

	public function afterValidation()
	{
		if ($this->votes_points >= 50) {
			$this->votes++;
			$this->votes_points = 0;
		}
	}

	public function afterCreate()
	{
		if ($this->id > 0) {
			$activity = new Activities();
			$activity->users_id = $this->id;
			$activity->type = 'U';
			$activity->save();
		}
	}

	public function getHumanKarma()
	{
		if ($this->karma >= 1000) {
			return sprintf("%.1f", $this->karma / 1000) . 'k';
		} else {
			return $this->karma;
		}
	}

}