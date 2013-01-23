<?php

$router = new Phalcon\Mvc\Router(false);

$router->add('/', array(
	'controller' => 'discussions',
	'action' => 'index'
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

$router->add('/reply/edit', array(
	'controller' => 'discussions',
	'action' => 'editReply'
));

$router->add('/reply/delete/{id:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'deleteReply'
));

$router->add('/discussion/{id:[0-9]+}/{slug}', array(
	'controller' => 'discussions',
	'action' => 'view'
));

$router->add('/user/{id:[0-9]+}/{login}', array(
	'controller' => 'discussions',
	'action' => 'user'
));

$router->add('/discussions/{order:[a-z]+}', array(
	'controller' => 'discussions',
	'action' => 'index'
));

$router->add('/discussions/{order:[a-z]+}/{offset:[0-9]+}', array(
	'controller' => 'discussions',
	'action' => 'index'
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

$router->add('/search', array(
	'controller' => 'discussions',
	'action' => 'search'
));

return $router;