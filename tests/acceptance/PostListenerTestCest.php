<?php

/*
   +------------------------------------------------------------------------+
   | Phosphorum                                                             |
   +------------------------------------------------------------------------+
   | Copyright (c) 2013-present Phalcon Team (https://www.phalconphp.com)   |
   +------------------------------------------------------------------------+
   | This source file is subject to the New BSD License that is bundled     |
   | with this package in the file LICENSE.txt.                             |
   |                                                                        |
   | If you did not receive a copy of the license and are unable to         |
   | obtain it through the world-wide-web, please send an email             |
   | to license@phalconphp.com so we can send you a copy immediately.       |
   +------------------------------------------------------------------------+
   | Authors: Sergii Svyrydenko <sergey.v.sviridenko@gmail.com>             |
   +------------------------------------------------------------------------+
 */

use Helper\Post;
use Helper\User;
use Helper\Category;
use Phosphorum\Model\Activities;
use Phosphorum\Model\PostsViews;
use Phosphorum\Model\PostsNotifications;
use Phosphorum\Model\Users;
use Phosphorum\Model\Notifications;
use Phosphorum\Model\Categories;
use Phosphorum\Model\PostsHistory;
use Phosphorum\Model\Posts;

/**
 * Test PostListener event manager
 */
class PostListenerCest
{
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    /** @var Post */
    protected $post;


    protected function _inject(Category $category, User $user, Post $post)
    {
        $this->user     = $user;
        $this->post     = $post;
        $this->category = $category;
    }

    /**
     * Test event `afterCreate`. Add row to table `activities`, `posts_notifications`, `posts_history`, `categories`
     */
    public function shouldCreateNewPost(AcceptanceTester $I)
    {
        $I->wantTo('Check post listener');

        $user  = $this->user->haveUserWithSession();
        $catId = $this->category->haveCategory();
        $category = Categories::findFirstById($catId);

        $postId = $this->post->havePost([
            'title'         => 'Test listener',
            'content'       => 'Test listener content',
            'slug'          => 'test_post_listener',
            'users_id'      => $user['id'],
            'categories_id' => $catId
        ]);

        $I->amOnPage("/discussion/{$postId}/test_post_listener");
        $I->seeInSource('Test listener');

        /*
         * Checking event `afterCreate` for PostListener without notification
         */
        $firstActivity = Activities::findFirst(["posts_id = {$postId}"]);
        $I->assertEquals($firstActivity->posts_id, $postId, "Posts ID aren't equals, table activities");
        $I->assertEquals($firstActivity->users_id, $user['id'], "User ID aren't equals, table activities");

        $firstPostNotif = PostsNotifications::findFirst(["posts_id = {$postId}"]);
        $I->assertEquals($firstPostNotif->posts_id, $postId, "Posts ID aren't equals, table posts_notification");
        $I->assertEquals($firstPostNotif->users_id, $user['id'], "User ID aren't equals, table posts_notification");

        $firstPostHistory = PostsHistory::findFirst(["posts_id = {$postId}"]);
        $I->assertEquals($firstPostHistory->posts_id, $postId, "Posts ID aren't equals, table posts_history");
        $I->assertEquals($firstPostHistory->users_id, $user['id'], "User ID aren't equals, table posts_history");

        /**
         * Check how many row added to posts_history
         * @issue https://github.com/phalcon/forum/issues/442
         */
        $I->assertEquals(PostsHistory::count(["posts_id = {$postId}"]), 1, "Added many row to posts_history table");

        $firstCat = Categories::findFirst("id = {$catId}");
        $I->assertEquals($firstCat->number_posts, ++$category->number_posts, "Amount number of post's in category hasn't been incremented, table categories");
    }

    /**
     * Test add row to notification table and row with IPaddres to posts_view
     */
    public function shouldAddNotification(AcceptanceTester $I)
    {
        $I->wantTo('Check post notification and ipaddress');
        $userNotifification = $this->user->haveUser([
            'notifications' => 'Y'
        ]);
        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test listener',
            'content'       => 'Test listener content',
            'slug'          => 'test_notification',
            'users_id'      => $user['id'],
            'categories_id' => $catId
        ]);

        $I->amOnPage("/discussion/{$postId}/test_notification");
        $I->seeInSource('Test listener');

        $firstNotif = Notifications::findFirst([
            'users_id' => $userNotifification['id'],
            'order' => 'id DESC'
        ]);
        $I->assertEquals($firstNotif->users_id, $userNotifification['id'], "Notifications hasn't been added, table notifications");

        $countIpAddress = PostsViews::count(["posts_id = {$postId}"]);

        $I->assertEquals($countIpAddress, 1, "IP address hasn't beed added to posts_view table");
    }

    /**
     * Test add slug to posts table
     */
    public function shouldAddValidSlug(AcceptanceTester $I)
    {
        $I->wantTo('Check post slug');

        $user  = $this->user->haveUser();
        $catId = $this->category->haveCategory();

        $postId = $this->post->havePost([
            'title'         => 'Test listener',
            'content'       => 'Test listener content',
            'slug'          => '',
            'users_id'      => $user['id'],
            'categories_id' => $catId
        ]);

        $post = Posts::findFirst(["id = {$postId}"]);
        $I->assertEquals($post->slug, 'test-listener', "Slug hasn't been added to table posts");
    }

    /**
     * Test edit button.
     * @todo this test should be added when auth tests will be added
     */
//    public function shouldCorrectEditTitleAndPost(AcceptanceTester $I)
//    {
//        $I->wantTo('Check edit button');
//
//        $user  = $this->user->haveUserWithSession();
//        $catId = $this->category->haveCategory();
//
//        $postId = $this->post->havePost([
//            'title'         => 'Test edit',
//            'content'       => 'Test listener. Edit button',
//            'slug'          => 'test_edit',
//            'users_id'      => $user['id'],
//            'categories_id' => $catId
//        ]);
//
//        $I->amOnPage("/discussion/{$postId}/test_edit");
//
//        $I->assertEquals(PostsHistory::count(
//            ["posts_id = {$postId}"]),
//            1,
//            "Added more than one row to posts_history table"
//        );
//        $I->seeInSource("/edit/discussion/{$postId}");
//        $I->amOnPage("/edit/discussion/{$postId}");
//        $I->fillField(['id' => 'title'], 'Test_edit1');
//        $I->click('Save');
//        $I->amOnPage("/discussion/{$postId}/testedit1");
//
//        $I->assertEquals(PostsHistory::count(
//            ["posts_id = {$postId}"]),
//            1,
//            "Count amount row in posts_history table shouldn't be 1"
//        );
//
//        $I->seeInSource("/edit/discussion/testedit1");
//        $I->amOnPage("/edit/discussion/testedit1");
//        $I->fillField(['id' => 'content'], 'Test edit content');
//        $I->click('Save');
//        $I->assertEquals(PostsHistory::count(
//            ["posts_id = {$postId}"]),
//            2,
//            "Count amount row in posts_history table wasn't 2"
//        );
//    }
}
