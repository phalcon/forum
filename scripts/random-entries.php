<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

use Phalcon\Di;
use Phalcon\Tag;
use Phosphorum\Models\Users;
use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsReplies;
use Phosphorum\Models\PostsPollOptions;
use Phosphorum\Models\Categories;
use Phalcon\Logger\Adapter\Stream;
use Faker\Factory as Faker;

/**
 * This script generates random posts
 */

require 'cli-bootstrap.php';

$faker = Faker::create();
$log   = new Stream('php://stdout');

$log->info('Start');

/** @var Phalcon\Db\AdapterInterface $database */
$database = Di::getDefault()->getShared('db');

$database->begin();

for ($i = 0; $i <= 20; $i++) {
    $title = $faker->company;

    $category               = new Categories();
    $category->name         = $title;
    $category->description  = $faker->sentence;
    $category->slug         = Tag::friendlyTitle($title);
    $category->number_posts = 0;
    $category->no_bounty    = 'N';
    $category->no_digest    = 'N';

    if (!$category->save()) {
        $database->rollback();
        die(join(PHP_EOL, $category->getMessages()));
    }

    $log->info('Category: ' . $category->name);
}

for ($i = 0; $i <= 50; $i++) {
    $user           = new Users();
    $user->name     = $faker->name;
    $user->login    = $faker->userName;
    $user->email    = $faker->email;
    $user->timezone = $faker->timezone;

    if (!$user->save()) {
        $database->rollback();
        die(join(PHP_EOL, $user->getMessages()));
    }

    $log->info('User: ' . $user->name);
}
$database->commit();

$categoryIds = Categories::find(['columns' => 'id'])->toArray();
$userIds     = Users::find(['columns' => 'id'])->toArray();

$database->begin();
for ($i = 0; $i <= 500; $i++) {
    $title   = $faker->company;

    $post          = new Posts();
    $post->title   = $title;
    $post->slug    = Tag::friendlyTitle($title);
    $post->content = $faker->text();

    $userRandId     = array_rand($userIds);
    $post->users_id = $userIds[$userRandId]['id'];

    $categoryRandId      = array_rand($categoryIds);
    $post->categories_id = $categoryIds[$categoryRandId]['id'];

    if (!$post->save()) {
        $database->rollback();
        die(join(PHP_EOL, $post->getMessages()));
    }

    if (!mt_rand(0, 10)) {
        $size = mt_rand(2, 10);
        $options = [];
        for ($j = 0; $j < $size; $j++) {
            $options[$j] = $faker->company;
        }

        foreach ($options as $opt) {
            $option          = new PostsPollOptions();
            $option->posts_id = $post->id;
            $option->title   = htmlspecialchars($opt, ENT_QUOTES);

            if (!$option->save()) {
                echo join(PHP_EOL, $option->getMessages()), PHP_EOL;
                $database->rollback();
                die;
            }

            $log->info('Option: ' . $option->title);
        }
    }

    $log->info('Post: ' . $post->title);
}
$database->commit();

$postIds = Posts::find(['columns' => 'id'])->toArray();

$database->begin();
for ($i = 0; $i <= 1000; $i++) {
    $reply = new PostsReplies();

    $reply->content = $faker->paragraph();

    $postRandId      = array_rand($postIds);
    $reply->posts_id = $postIds[$postRandId]['id'];

    $userRandId      = array_rand($userIds);
    $reply->users_id = $userIds[$userRandId]['id'];

    if (!$reply->save()) {
        $database->rollback();
        die(join(PHP_EOL, $reply->getMessages()));
    }

    $reply->post->number_replies++;
    $reply->post->modified_at = time();
    $reply->save();

    $log->info('Reply to post: ' . $reply->posts_id);
}

$database->commit();
