<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

use Phalcon\Mvc\Router;
use Phosphorum\Http\Filter\Ajax;

$router = new Router(false);
$router->removeExtraSlashes(true);

$router->add(
    '/help/stats',
    [
       'controller' => 'help',
       'action'     => 'stats'
    ]
);

$router->add(
    '/help/about',
    [
       'controller' => 'help',
       'action'     => 'about'
    ]
);

$router->add(
    '/help/moderators',
    [
       'controller' => 'help',
       'action'     => 'moderators'
    ]
);

$router->add(
    '/help/voting',
    [
       'controller' => 'help',
       'action'     => 'voting'
    ]
);

$router->add(
    '/help/markdown',
    [
       'controller' => 'help',
       'action'     => 'markdown'
    ]
);

$router->add(
    '/help/karma',
    [
       'controller' => 'help',
       'action'     => 'karma'
    ]
);

$router->add(
    '/help/badges',
    [
       'controller' => 'help',
       'action'     => 'badges'
    ]
);

$router->add(
    '/help/create-post',
    [
       'controller' => 'help',
       'action'     => 'create'
    ]
);

$router->add(
    '/help',
    [
       'controller' => 'help',
       'action'     => 'index'
    ]
);

$router->add(
    '/discussions',
    [
       'controller' => 'discussions',
       'action'     => 'index'
    ]
);

$router->add(
    '/hook/mail-bounce',
    [
       'controller' => 'hooks',
       'action'     => 'mailBounce'
    ]
);

$router->add(
    '/hook/mail-reply',
    [
       'controller' => 'hooks',
       'action'     => 'mailReply'
    ]
);

$router->add(
    '/search',
    [
       'controller' => 'discussions',
       'action'     => 'search'
    ]
);

$router->addPost(
    '/preview',
    [
       'controller' => 'utils',
       'action'     => 'preview'
    ]
);

$router->add(
    '/reply/accept/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'accept'
    ]
);

$router->add(
    '/reply/vote-up/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'voteUp'
    ]
);

$router->add(
    '/reply/vote-down/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'voteDown'
    ]
);

$router->add(
    '/reply/history/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'history'
    ]
)->beforeMatch([new Ajax, 'check']);

$router->add(
    '/discussion/history/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'history'
    ]
)->beforeMatch([new Ajax, 'check']);

$router->add(
    '/discussion/vote-up/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'voteUp'
    ]
);

$router->add(
    '/poll/vote/{id:[0-9]+}/{option:[0-9]+}',
    [
        'controller' => 'polls',
        'action'     => 'vote'
    ]
);

$router->add(
    '/discussion/vote-down/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'voteDown'
    ]
);

$router->add(
    '/login/oauth/authorize',
    [
       'controller' => 'session',
       'action'     => 'authorize'
    ]
);

$router->add(
    '/login/oauth/access_token/',
    [
       'controller' => 'session',
       'action'     => 'accessToken'
    ]
);

$router->add(
    '/login/oauth/access_token',
    [
       'controller' => 'session',
       'action'     => 'accessToken'
    ]
);

$router->add(
    '/logout',
    [
       'controller' => 'session',
       'action'     => 'logout'
    ]
);

$router->add(
    '/find-related',
    [
       'controller' => 'discussions',
       'action'     => 'findRelated'
    ]
);

$router->add(
    '/show-related',
    [
       'controller' => 'discussions',
       'action'     => 'showRelated'
    ]
);

$router->add(
    '/notifications',
    [
       'controller' => 'discussions',
       'action'     => 'notifications'
    ]
);

$router->add(
    '/activity',
    [
       'controller' => 'discussions',
       'action'     => 'activity'
    ]
);

$router->add(
    '/activity/irc',
    [
       'controller' => 'discussions',
       'action'     => 'irc'
    ]
);

$router->add(
    '/delete/discussion/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'delete'
    ]
);

$router->add(
    '/category/{id:[0-9]+}/{slug}/{offset:[0-9]+}',
    [
       'controller' => 'categories',
       'action'     => 'view'
    ]
);

$router->add(
    '/category/{id:[0-9]+}/{slug}',
    [
        'controller' => 'categories',
        'action'     => 'view'
    ]
);

$router->add(
    '/reload-categories',
    [
        'controller' => 'categories',
        'action'     => 'reloadCategories'
    ]
);

$router->add(
    '/post/discussion',
    [
       'controller' => 'discussions',
       'action'     => 'create'
    ]
);

$router->add(
    '/edit/discussion/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'edit'
    ]
);

$router->add(
    '/stick/discussion/{id:[0-9]+}',
    [
        'controller' => 'discussions',
        'action'     => 'stick'
    ]
);

$router->add(
    '/unstick/discussion/{id:[0-9]+}',
    [
        'controller' => 'discussions',
        'action'     => 'unstick'
    ]
);


$router->add(
    '/subscribe/discussion/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'subscribe'
    ]
);

$router->add(
    '/unsubscribe/discussion/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'unsubscribe'
    ]
);

$router->add(
    '/user/{id:[0-9]+}/{login}',
    [
       'controller' => 'users',
       'action'     => 'view'
    ]
);

$router->add(
    '/settings',
    [
        'controller' => 'users',
        'action'     => 'settings'
    ]
);

$router->add(
    '/reply/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'get'
    ]
);

$router->add(
    '/reply/update',
    [
       'controller' => 'replies',
       'action'     => 'update'
    ]
);

$router->add(
    '/reply/delete/{id:[0-9]+}',
    [
       'controller' => 'replies',
       'action'     => 'delete'
    ]
);

$router->add(
    '/discussions/{order:[a-z]+}',
    [
       'controller' => 'discussions',
       'action'     => 'index'
    ]
);

$router->add(
    '/discussions/{order:[a-z]+}/{offset:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'index'
    ]
);

$router->add(
    '/discussion/{id:[0-9]+}/{slug}',
    [
       'controller' => 'discussions',
       'action'     => 'view'
    ]
);

$router->add(
    '/',
    [
       'controller' => 'discussions',
       'action'     => 'index'
    ]
);

return $router;
