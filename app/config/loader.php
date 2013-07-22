<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
	'Phosphorum\Models' => $config->application->modelsDir,
	'Phosphorum\Controllers' => $config->application->controllersDir,
	'Phosphorum\Github' => $config->application->libraryDir . '/Github',
	'Phosphorum\Amazon' => $config->application->libraryDir . '/Amazon',
));

$loader->register();