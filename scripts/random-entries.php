<?php

/**
 * This scripts generates random posts
 */

require 'cli-bootstrap.php';

$faker = Faker\Factory::create();

/** @var Phalcon\Db\AdapterInterface $database */
$database = $di->getShared('db');

$database->begin();

for ($i = 0; $i <= 20; $i++) {

    $title = $faker->company;

    $post       = new Phosphorum\Models\Categories();
    $post->name = $title;
    $post->slug = Phalcon\Tag::friendlyTitle($title);

    if (!$post->save()) {

        var_dump($post->getMessages());
        break;
    }
}
$database->commit();

$database->begin();
for ($i = 0; $i <= 20; $i++) {

    $title = $faker->company;

    $user        = new Phosphorum\Models\Users();
    $user->name  = $faker->name;
    $user->login = $faker->userName;

    if (!$user->save()) {

        var_dump($user->getMessages());
        break;
    }
}
$database->commit();

$categoryIds = Phosphorum\Models\Categories::find(['columns' => 'id'])->toArray();
$usersIds    = Phosphorum\Models\Users::find(['columns' => 'id'])->toArray();


$database->begin();
for ($i = 0; $i <= 500; $i++) {

    $title   = $faker->company;
    $content = $faker->text();

    $post          = new Phosphorum\Models\Posts();
    $post->title   = $title;
    $post->slug    = Phalcon\Tag::friendlyTitle($title);
    $post->content = $content;


    $userRandId     = array_rand($usersIds);
    $post->users_id = $usersIds[$userRandId]['id'];

    $categoryRandId      = array_rand($categoryIds);
    $post->categories_id = $categoryIds[$categoryRandId]['id'];

    if (!$post->save()) {

        var_dump($post->getMessages());
        break;
    }

    $post->category->number_posts++;

    if (!$post->category->save()) {

        var_dump($post->category->getMessages());
        break;
    }
}
$database->commit();

