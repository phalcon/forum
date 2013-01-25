<?php

return new \Phalcon\Config(array(
	'database' => array(
		'adapter'  => 'Mysql',
		'host'     => 'localhost',
		'username' => 'root',
		'password' => '',
		'name'     => 'forum',
	),
	'application' => array(
		'controllersDir' => __DIR__ . '/../../app/controllers/',
		'modelsDir'      => __DIR__ . '/../../app/models/',
		'viewsDir'       => __DIR__ . '/../../app/views/',
		'pluginsDir'     => __DIR__ . '/../../app/plugins/',
		'libraryDir'     => __DIR__ . '/../../app/library/',
		'baseUri'        => '/',
	),
	'models' => array(
		'metadata' => array(
			'adapter' => 'Memory'
		)
	),
	'github' => array(
		'clientId' => '',
		'clientSecret' => '',
		'redirectUri' => 'http://forum.phalconphp.com/forum/login/oauth/access_token'
	),
	'amazon' => array(
		'AWSAccessKeyId' => '',
		'AWSSecretKey' => ''
	)
));
