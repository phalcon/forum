<?php

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
	'Forum\Controllers' => $config->application->controllersDir,
	'Forum\Models' => $config->application->modelsDir,
	'Forum\Github' => $config->application->libraryDir . '/Github',
));