<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/


/**
 * This script generates random posts
 */

require 'cli-bootstrap.php';

$faker = Faker\Factory::create();
$log   = new Phalcon\Logger\Adapter\Stream('php://stdout');

$log->info('Start');

/** @var Phalcon\Db\AdapterInterface $database */
$database = $di->getShared('db');

$database->begin();

for ($i = 0; $i <= 20; $i++) {

    $title = $faker->company;

    $category               = new Phosphorum\Models\Categories();
    $category->name         = $title;
    $category->description  = $faker->sentence;
    $category->slug         = Phalcon\Tag::friendlyTitle($title);
    $category->number_posts = 0;

    if (!$category->save()) {

        var_dump($category->getMessages());
        $database->rollback();
        die;
    }

    $log->info('Category: ' . $category->name);
}

for ($i = 0; $i <= 50; $i++) {

    $user           = new Phosphorum\Models\Users();
    $user->name     = $faker->name;
    $user->login    = $faker->userName;
    $user->email    = $faker->email;
    $user->timezone = $faker->timezone;

    if (!$user->save()) {

        var_dump($user->getMessages());
        $database->rollback();
        die;
    }

    $log->info('User: ' . $user->name);
}
$database->commit();

$categoryIds = Phosphorum\Models\Categories::find(['columns' => 'id'])->toArray();
$userIds     = Phosphorum\Models\Users::find(['columns' => 'id'])->toArray();

$database->begin();
for ($i = 0; $i <= 500; $i++) {

    $title   = $faker->company;
    $content = $faker->text();

    $post          = new Phosphorum\Models\Posts();
    $post->title   = $title;
    $post->slug    = Phalcon\Tag::friendlyTitle($title);
    $post->content = $content;


    $userRandId     = array_rand($userIds);
    $post->users_id = $userIds[$userRandId]['id'];

    $categoryRandId      = array_rand($categoryIds);
    $post->categories_id = $categoryIds[$categoryRandId]['id'];

    if (!$post->save()) {

        var_dump($post->getMessages());
        $database->rollback();
        die;
    }

    $log->info('Post: ' . $post->title);
}
$database->commit();

$postIds = Phosphorum\Models\Posts::find(['columns' => 'id'])->toArray();

$database->begin();
for ($i = 0; $i <= 1000; $i++) {

    $reply = new \Phosphorum\Models\PostsReplies();

    $reply->content = $faker->paragraph();

    $postRandId      = array_rand($postIds);
    $reply->posts_id = $postIds[$postRandId]['id'];

    $userRandId      = array_rand($userIds);
    $reply->users_id = $userIds[$userRandId]['id'];

    if (!$reply->save()) {

        var_dump($reply->getMessages());
        $database->rollback();
        die;
    }

    $reply->post->number_replies++;
    $reply->save();

    $log->info('Reply to post: ' . $reply->posts_id);
}

$database->commit();
