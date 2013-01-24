<?php

$router = new Phalcon\Mvc\Router(false);

$router->add('/', array(
	'controller' => 'discussions',
	'action' => 'index'
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

$router->add('/user/{id:[0-9]+}/{login}', array(
	'controller' => 'discussions',
	'action' => 'user'
));

$router->add('/category/{id:[0-9]+}/{slug}', array(
	'controller' => 'discussions',
	'action' => 'category'
));

$router->add('/category/{id:[0-9]+}/{slug}/{offset:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'category'
));

$router->add('/activity', array(
	'controller' => 'discussions',
	'action' => 'activity'
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

$router->add('/post/discussion', array(
	'controller' => 'discussions',
	'action' => 'create'
));

$router->add('/edit/discussion/{id:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'edit'
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

$router->add('/search', array(
	'controller' => 'discussions',
	'action' => 'search'
));

$router->add('/settings', array(
	'controller' => 'discussions',
	'action' => 'settings'
));

return $router;