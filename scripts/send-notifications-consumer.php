<?php

/**
 * This scripts generates random posts
 */
require 'cli-bootstrap.php';

use Phosphorum\Mail\SendSpool;

class SendSpoolConsumerTask extends Phalcon\DI\Injectable
{

	public function run()
	{
		$spool = new SendSpool();
		$spool->consumeQueue();
	}

}

try {
	$task = new SendSpoolConsumerTask($config);
	$task->run();
} catch (Exception $e) {
	echo $e->getMessage(), PHP_EOL;
	echo $e->getTraceAsString();
}
