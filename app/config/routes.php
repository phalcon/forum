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

$router = new Phalcon\Mvc\Router(false);

$router->add(
    '/sitemap',
    array(
       'controller' => 'sitemap',
       'action'     => 'index'
    )
);

$router->add(
    '/help/moderators',
    array(
       'controller' => 'help',
       'action'     => 'moderators'
    )
);

$router->add(
    '/help/voting',
    array(
       'controller' => 'help',
       'action'     => 'voting'
    )
);

$router->add(
    '/help/markdown',
    array(
       'controller' => 'help',
       'action'     => 'markdown'
    )
);

$router->add(
    '/help/karma',
    array(
       'controller' => 'help',
       'action'     => 'karma'
    )
);

$router->add(
    '/help',
    array(
       'controller' => 'help',
       'action'     => 'index'
    )
);

$router->add(
    '/index.html',
    array(
       'controller' => 'discussions',
       'action'     => 'index'
    )
);

$router->add(
    '/discussions',
    array(
       'controller' => 'discussions',
       'action'     => 'index'
    )
);

$router->add(
    '/hook/mail-bounce',
    array(
       'controller' => 'hooks',
       'action'     => 'mailBounce'
    )
);

$router->add(
    '/hook/mail-reply',
    array(
       'controller' => 'hooks',
       'action'     => 'mailReply'
    )
);

$router->add(
    '/search',
    array(
       'controller' => 'discussions',
       'action'     => 'search'
    )
);

$router->add(
    '/settings',
    array(
       'controller' => 'discussions',
       'action'     => 'settings'
    )
);

$router->add(
    '/reload-categories',
    array(
       'controller' => 'discussions',
       'action'     => 'reloadCategories'
    )
);

$router->addPost(
    '/preview',
    array(
       'controller' => 'utils',
       'action'     => 'preview'
    )
);

$router->add(
    '/reply/accept/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'accept'
    )
);

$router->add(
    '/reply/vote-up/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'voteUp'
    )
);

$router->add(
    '/reply/vote-down/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'voteDown'
    )
);


$router->add(
    '/reply/history/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'history'
    )
);

$router->add(
    '/discussion/history/{id:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'history'
    )
);

$router->add(
    '/discussion/vote-up/{id:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'voteUp'
    )
);

$router->add(
    '/discussion/vote-down/{id:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'voteDown'
    )
);

$router->add(
    '/login/oauth/authorize',
    array(
       'controller' => 'session',
       'action'     => 'authorize'
    )
);

$router->add(
    '/login/oauth/access_token/',
    array(
       'controller' => 'session',
       'action'     => 'accessToken'
    )
);

$router->add(
    '/logout',
    array(
       'controller' => 'session',
       'action'     => 'logout'
    )
);

$router->add(
    '/activity',
    array(
       'controller' => 'discussions',
       'action'     => 'activity'
    )
);

$router->add(
    '/activity/irc',
    array(
       'controller' => 'discussions',
       'action'     => 'irc'
    )
);

$router->add(
    '/delete/discussion/{id:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'delete'
    )
);

$router->add(
    '/category/{id:[0-9]+}/{slug}/{offset:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'category'
    )
);

$router->add(
    '/post/discussion',
    array(
       'controller' => 'discussions',
       'action'     => 'create'
    )
);

$router->add(
    '/edit/discussion/{id:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'edit'
    )
);

$router->add(
    '/user/{id:[0-9]+}/{login}',
    array(
       'controller' => 'discussions',
       'action'     => 'user'
    )
);

$router->add(
    '/category/{id:[0-9]+}/{slug}',
    array(
       'controller' => 'discussions',
       'action'     => 'category'
    )
);

$router->add(
    '/reply/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'get'
    )
);

$router->add(
    '/reply/update',
    array(
       'controller' => 'replies',
       'action'     => 'update'
    )
);

$router->add(
    '/reply/delete/{id:[0-9]+}',
    array(
       'controller' => 'replies',
       'action'     => 'delete'
    )
);

$router->add(
    '/discussions/{order:[a-z]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'index'
    )
);

$router->add(
    '/discussions/{order:[a-z]+}/{offset:[0-9]+}',
    array(
       'controller' => 'discussions',
       'action'     => 'index'
    )
);

$router->add(
    '/discussion/{id:[0-9]+}/{slug}',
    array(
       'controller' => 'discussions',
       'action'     => 'view'
    )
);

$router->add(
    '/',
    array(
       'controller' => 'discussions',
       'action'     => 'index'
    )
);

return $router;
