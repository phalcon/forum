<?php

namespace Forum\Controllers;

use Forum\Models\Posts,
	Forum\Models\PostsViews,
	Forum\Models\PostsReplies,
	Forum\Models\Categories,
	Forum\Models\Activities,
	Phalcon\Tag;

class DiscussionsController extends \Phalcon\Mvc\Controller
{

	protected function prepareQueries()
	{

		$itemBuilder = $this->modelsManager->createBuilder()
			->from(array(
				'p' => 'Forum\Models\Posts'
			))
			->join('Forum\Models\Users', null, 'u')
			->join('Forum\Models\Categories', null, 'c')
			->orderBy('p.created_at DESC');

		$totalBuilder = clone $itemBuilder;

		$itemBuilder->columns(array(
			'p.id',
			'p.title',
			'p.slug',
			'p.number_replies',
			'p.number_views',
			'p.created_at',
			'user_name' => 'u.name',
			'user_login' => 'u.login',
			'user_id' => 'u.id',
			'category_name' => 'c.name',
			'category_slug' => 'c.slug',
			'category_id' => 'c.id'
		))
		->limit(30);

		$totalBuilder->columns('COUNT(*)');

		/**
		 * Query the categories ordering them by number_posts
		 */
		if (!$this->view->getCache()->exists('sidebar')) {
			$this->view->categories = Categories::find(array(
				'order' => 'number_posts DESC, name'
			));
		}

		return array($itemBuilder, $totalBuilder);
	}

	/**
	 * Shows latest posts using an order clause
	 */
	public function indexAction($order=null, $offset=0)
	{

		list($itemBuilder, $totalBuilder) = $this->prepareQueries();

		/**
		 * Create the conditions according to the order parameter
		 */
		$params = null;
		switch ($order) {
			case 'my':
				Tag::setTitle('My Discussions');
				$userId = $this->session->get('identity');
				if ($userId) {
					$params = array($userId);
					$itemBuilder->where('p.users_id = ?0');
					$totalBuilder->where('p.users_id = ?0');
				}
				break;
			case 'unanswered':
				Tag::setTitle('Unanswered Discussions');
				$itemBuilder->where('p.number_replies = 0');
				$totalBuilder->where('p.number_replies = 0');
				break;
			default:
				Tag::setTitle('Discussions');
		}

		$itemBuilder->offset($offset);

		$this->view->posts = $itemBuilder->getQuery()
			->execute($params);

		$this->view->total_posts = $totalBuilder
			->getQuery()
			->setUniqueRow(true)
			->execute($params);

		if (!$order) {
			$order = 'new';
		}

		$this->view->currentOrder = $order;
		$this->view->offset = $offset;
		$this->view->paginatorUri = 'discussions/' . $order;
	}

	/**
	 * Shows latest posts by category
	 */
	public function categoryAction($categoryId, $slug, $offset=0)
	{
		Tag::setTitle('Discussions');

		$category = Categories::findFirstById($categoryId);
		if (!$category) {
			$this->flashSession->notice('The category doesn\'t exist');
			return $this->response->redirect();
		}

		list($itemBuilder, $totalBuilder) = $this->prepareQueries();

		$totalBuilder->where('p.categories_id = ?0');

		$itemBuilder->where('p.categories_id = ?0')
			->orderBy('p.created_at DESC');

		$itemBuilder->offset($offset);

		$posts = $itemBuilder->getQuery()
			->execute(array($categoryId));

		if (!count($posts)) {
			$this->flashSession->notice('There are no posts in category: '.$category->name);
			return $this->response->redirect();
		}

		$total_posts = $totalBuilder
			->getQuery()
			->setUniqueRow(true)
			->execute(array($categoryId));

		$this->view->posts = $posts;
		$this->view->total_posts = $total_posts;
		$this->view->currentOrder = null;
		$this->view->offset = $offset;
		$this->view->paginatorUri = 'category/' . $category->id . '/' . $category->slug;
	}

	/**
	 * This shows the create post form and also store the related post
	 */
	public function createAction()
	{

		$usersId = $this->session->get('identity');
		if (!$usersId) {
			$this->flashSession->error('You must be logged first');
			return $this->response->redirect();
		}

		\Phalcon\Tag::setTitle('Start a Discussion');

		if ($this->request->isPost()) {

			$title = $this->request->getPost('title', 'trim');

			/**
			 * Create a posts-views logging the ipaddress where the post was created
			 * This avoids that the same session counts as post view
			 */
			$postView = new PostsViews();
			$postView->ipaddress = $this->request->getClientAddress();

			$post = new Posts();
			$post->users_id = $usersId;
			$post->categories_id = $this->request->getPost('categoryId');
			$post->title = $title;
			$post->slug = Tag::friendlyTitle($title);
			$post->content = $this->request->getPost('content');
			$post->views = $postView;

			if ($post->save()) {

				/**
				 * Refresh sidebar
				 */
				$this->view->getCache()->delete('sidebar');

				/**
				 * Update the total of posts related to a category
				 */
				$post->category->number_posts++;
				$post->category->save();

				return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
			}

			foreach ($post->getMessages() as $message) {
				$this->flash->error($message);
			}
		}

		$this->view->categories = Categories::find(array(
			'order' => 'name'
		));
	}

	/**
	 * Displays a post and its comments
	 */
	public function viewAction($id, $slug)
	{

		if (!$this->request->isPost()) {

			/**
			 * Find the post using get
			 */
			$post = Posts::findFirstById($id);
			if (!$post) {
				$this->flashSession->error('The discussion does not exist');
				return $this->response->redirect();
			}

			$ipAddress = $this->request->getClientAddress();

			$viewed = PostsViews::count(array(
				'posts_id = ?0 AND ipaddress = ?1',
				'bind' => array($id, $ipAddress)
			));

			/**
			 * A view is stored by ipaddress
			 */
			if (!$viewed) {

				/**
				 * Increase the number of views in the post
				 */
				$post->number_views++;

				$postView = new PostsViews();
				$postView->post = $post;
				$postView->ipaddress = $ipAddress;
				if (!$postView->save()) {
					var_dump($postView->getMessages());
				}
			}
		} else {

			/**
			 * Find the post using POST
			 */
			$post = Posts::findFirstById($this->request->getPost('id'));
			if (!$post) {
				$this->flashSession->error('The discussion does not exist');
				return $this->response->redirect();
			}

			$content = $this->request->getPost('content');

			if (trim($content)) {

				$usersId = $this->session->get('identity');

				/**
				 * Only update the number of replies if the user that commented isn't the same that posted
				 */
				if ($post->users_id != $usersId) {
					$post->number_replies++;
				}

				$postReply = new PostsReplies();
				$postReply->post = $post;
				$postReply->users_id = $usersId;
				$postReply->content = $content;

				if (!$postReply->save()) {
					var_dump($postReply->getMessages());
				} else {
					Tag::resetInput();
				}
			}
		}

		\Phalcon\Tag::setTitle($post->title . ' - Discussion');

		$this->view->post = $post;
	}

	/**
	 * Shows the latest activity on the forum
	 */
	public function activityAction($offset=0)
	{

		$this->view->total = Activities::count();

		$this->view->activities = Activities::find(array(
			'order' => 'created_at DESC',
			'limit' => array('number' => 30, 'offset' => 0)
		));

		\Phalcon\Tag::setTitle('Recent Activity');
	}

	public function userAction()
	{

	}

	public function searchAction()
	{

		Tag::setTitle('Search Results');

		list($itemBuilder, $totalBuilder) = $this->prepareQueries();

		$q = $this->request->getQuery('q');

		$queryTerms = '%'.preg_replace('/[ \t]+/', '%', $q).'%';

		$totalBuilder->where('p.title LIKE ?0');

		$itemBuilder->where('p.title LIKE ?0')
			->orderBy('p.created_at DESC');

		$posts = $itemBuilder->getQuery()
			->execute(array($queryTerms));

		if (!count($posts)) {
			$this->flashSession->notice('There are no search results');
			return $this->response->redirect();
		}

		$total_posts = $totalBuilder
			->getQuery()
			->setUniqueRow(true)
			->execute(array($queryTerms));

		$this->view->posts = $posts;
		$this->view->total_posts = $total_posts;
		$this->view->currentOrder = null;
		$this->view->offset = 0;
		$this->view->paginatorUri = 'search';

	}

}
