<?php

namespace Phosphorum\Models;

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

	public $created_at;

	public $number_views;

	public $number_replies;

	public function initialize()
	{
		$this->belongsTo('users_id', 'Phosphorum\Models\Users', 'id', array(
			'alias' => 'user'
		));

		$this->belongsTo('categories_id', 'Phosphorum\Models\Categories', 'id', array(
			'alias' => 'category',
			'foreignKey' => array(
				'message' => 'The category is not valid'
			)
		));

		$this->hasMany('id', 'Phosphorum\Models\PostsReplies', 'posts_id', array(
			'alias' => 'replies'
		));

		$this->hasMany('id', 'Phosphorum\Models\PostsViews', 'posts_id', array(
			'alias' => 'views'
		));

		$this->addBehavior(new Timestampable(array(
			'beforeCreate' => array(
				'field' => 'created_at'
			)
		)));
	}

	public function beforeValidationOnCreate()
	{
		$this->number_views = 0;
		$this->number_replies = 0;
	}

	/**
	 * Create a posts-views logging the ipaddress where the post was created
	 * This avoids that the same session counts as post view
	 */
	public function beforeCreate()
	{
		$postView = new PostsViews();
		$postView->ipaddress = $this->getDI()->getRequest()->getClientAddress();
		$this->views = $postView;
	}

	public function afterCreate()
	{
		/**
		 * Register a new activity
		 */
		if ($this->id > 0) {

			/**
			 * Register the activity
			 */
			$activity = new Activities();
			$activity->users_id = $this->users_id;
			$activity->posts_id = $this->id;
			$activity->type = 'P';
			$activity->save();

			/**
			 * Notify users that always want notifications
			 */
			$notification = new PostsNotifications();
			$notification->users_id = $this->users_id;
			$notification->posts_id = $this->id;
			$notification->save();

			/**
			 * Notify users that always want notifications
			 */
			foreach (Users::find('notifications = "Y"') as $user) {
				if ($this->users_id != $user->id) {
					$notification = new Notifications();
					$notification->users_id = $user->id;
					$notification->posts_id = $this->id;
					$notification->type = 'P';
					$notification->save();
				}
			}

			/**
			 * Update the total of posts related to a category
			 */
			$this->category->number_posts++;
			$this->category->save();
		}
	}

}