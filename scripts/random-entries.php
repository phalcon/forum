<?php

/**
 * This scripts generates random posts
 */

require 'cli-bootstrap.php';


for ( $i = 0; $i <= 20; $i++ ) {

    $title   = Phalcon\Text::random(Phalcon\Text::RANDOM_ALNUM , 50);
    $content = Phalcon\Text::random(Phalcon\Text::RANDOM_ALNUM , 265);

    $title   = chunk_split($title , rand(3 , 10) , ' ');
    $content = chunk_split($content , rand(3 , 10) , ' ');


    $post       = new Phosphorum\Models\Categories();
    $post->name = $title;
    $post->slug = Phalcon\Tag::friendlyTitle($title);

    if ( !$post->save() ) {

        var_dump($post->getMessages());
        break;
    }

}

for ( $i = 0; $i <= 500; $i++ ) {

    $title   = Phalcon\Text::random(Phalcon\Text::RANDOM_ALNUM , 50);
    $content = Phalcon\Text::random(Phalcon\Text::RANDOM_ALNUM , 265);

    $title   = chunk_split($title , rand(3 , 10) , ' ');
    $content = chunk_split($content , rand(3 , 10) , ' ');

    $post                = new Phosphorum\Models\Posts();
    $post->title         = $title;
    $post->slug          = Phalcon\Tag::friendlyTitle($title);
    $post->content       = $content;
    $post->users_id      = 1;
    $post->categories_id = mt_rand(1 , 20);

    if ( !$post->save() ) {

        var_dump($post->getMessages());
        break;
    }

    $post->category->number_posts++;

    if ( !$post->category->save() ) {

        var_dump($post->category->getMessages());
        break;
    }
}

