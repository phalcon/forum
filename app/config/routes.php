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

use Phalcon\Mvc\Router;
use Phosphorum\Http\Filter\Ajax;

$router = new Router(false);

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

$router->addGet('/search', 'Search::index')
       ->setName('search');

$router->addPost('/find-related', 'Search::findRelated')
       ->setName('find-related');

$router->addPost('/show-related', 'Search::showRelated')
       ->setName('show-related');

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
)->beforeMatch([new Ajax(), 'check']);

$router->add(
    '/discussion/history/{id:[0-9]+}',
    [
       'controller' => 'discussions',
       'action'     => 'history'
    ]
)->beforeMatch([new Ajax(), 'check']);

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
    '/category/{id:[0-9]+}',
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

$router->add('/discussions/{order:[a-z]+}', 'Discussions::index')
    ->setName('discussions-order');

$router->add('/discussions/{order:[a-z]+}/{offset:[0-9]+}', 'Discussions::index')
    ->setName('discussions-order-offset');

$router->add('/discussion/{id:[0-9]+}/{slug}', 'Discussions::view')
    ->setName('discussions-view');

$router->addGet('/400', 'Error::route400')
    ->setName('error-400');

$router->addGet('/401', 'Error::route401')
    ->setName('error-401');

$router->addGet('/403', 'Error::route403')
    ->setName('error-403');

$router->addGet('/404', 'Error::route404')
    ->setName('error-404');

$router->addGet('/500', 'Error::route500')
    ->setName('error-500');

$router->addGet('/503', 'Error::route503')
    ->setName('error-503');

$router->add('/', 'Discussions::index')
    ->setName('discussions-index');

return $router;
