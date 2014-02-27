<?php

$router = new Phalcon\Mvc\Router(false);

$router->add('/sitemap', array(
	'controller' => 'sitemap',
	'action' => 'index'
));

$router->add('/help', array(
	'controller' => 'discussions',
	'action' => 'help'
));

$router->add('/search', array(
	'controller' => 'discussions',
	'action' => 'search'
));

$router->add('/settings', array(
	'controller' => 'discussions',
	'action' => 'settings'
));

$router->add('/login/oauth/authorize', array(
	'controller' => 'session',
	'action' => 'authorize'
));

$router->add('/login/oauth/access_token', array(
	'controller' => 'session',
	'action' => 'accessToken'
));

$router->add('/logout', array(
	'controller' => 'session',
	'action' => 'logout'
));

$router->add('/category/{id:[0-9]+}/{slug}/{offset:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'category'
));

$router->add('/activity', array(
	'controller' => 'discussions',
	'action' => 'activity'
));

$router->add('/activity/irc', array(
	'controller' => 'discussions',
	'action' => 'irc'
));

$router->add('/post/discussion', array(
	'controller' => 'discussions',
	'action' => 'create'
));

$router->add('/edit/discussion/{id:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'edit'
));

$router->add('/user/{id:[0-9]+}/{login}', array(
	'controller' => 'discussions',
	'action' => 'user'
));

$router->add('/category/{id:[0-9]+}/{slug}', array(
	'controller' => 'discussions',
	'action' => 'category'
));

$router->add('/reply/{id:[0-9]+}', array(
	'controller' => 'replies',
	'action' => 'get'
));

$router->add('/reply/update', array(
	'controller' => 'replies',
	'action' => 'update'
));

$router->add('/reply/delete/{id:[0-9]+}', array(
	'controller' => 'replies',
	'action' => 'delete'
));

$router->add('/discussions/{order:[a-z]+}', array(
	'controller' => 'discussions',
	'action' => 'index'
));

$router->add('/discussions/{order:[a-z]+}/{offset:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'index'
));

$router->add('/discussion/{id:[0-9]+}/{slug}', array(
	'controller' => 'discussions',
	'action' => 'view'
));

$router->add('/discussions', array(
	'controller' => 'discussions',
	'action' => 'index'
));

$router->add('/discussion/history/{id:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'history'
));

$router->add('/index.html', array(
	'controller' => 'discussions',
	'action' => 'index'
));

$router->add('/', array(
	'controller' => 'discussions',
	'action' => 'index'
));

return $router;