<?php

/**
 * This script generates random posts
 */

error_reporting(E_ALL);

/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";

$loader = new \Phalcon\Loader();

/**
 * Include the loader
 */
require __DIR__ . "/../app/config/loader.php";

$loader->register();

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new \Phalcon\DI\FactoryDefault();

/**
 * Include the application services
 */
require __DIR__ . "/../app/config/services.php";

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-?!"·$%/()=[]´ç+*\`\'#';
$n = strlen($characters);

function generateRandomString($length, $characters, $n) {
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $n - 1)];
    }
    return $randomString;
}

for ($i = 0; $i <= 500; $i++) {

	$title = generateRandomString(72, $characters, $n);

	$content = generateRandomString(265, $characters, $n);

	$post = new Forum\Models\Posts();
	$post->title = $title;
	$post->slug = Phalcon\Tag::friendlyTitle($title);
	$post->content = $content;
	$post->users_id = 1;
	$post->categories_id = mt_rand(1, 20);
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

