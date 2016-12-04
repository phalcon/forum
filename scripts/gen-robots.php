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

/**
 * This script generates backup and uploads it to Dropbox
 */
require 'cli-bootstrap.php';

use Phalcon\Di;
use Phalcon\Config;
use Phalcon\DI\Injectable;
use League\Flysystem\Filesystem;
use Phalcon\Logger\Adapter\Stream;
use League\Flysystem\Adapter\Local;

class GenerateRobotsFile extends Injectable
{
    public function run()
    {
        $log = new Stream('php://stdout');

        /** @var Config $config */
        $config = Di::getDefault()->getShared('config');

        $expireDate = new DateTime('now', new DateTimeZone('UTC'));
        $expireDate->modify('+1 month');

        $baseUrl = rtrim($config->get('site')->url, '/');
        $content=<<<EOL
User-agent: *
Disallow: /400
Disallow: /401
Disallow: /403
Disallow: /404
Disallow: /500
Disallow: /503
Allow: /
Sitemap: $baseUrl/sitemap.xml
EOL;

        $adapter = new Local(dirname(dirname(__FILE__)) . '/public');
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has('robots.txt')) {
            $result = $filesystem->update('robots.txt', $content);
        } else {
            $result = $filesystem->write('robots.txt', $content);
        }

        if ($result) {
            $log->info('The robots.txt was successfully updated');
        } else {
            $log->error('Failed to update the robots.txt file');
        }
    }
}

try {
    $task = new GenerateRobotsFile();
    $task->run();
} catch (Exception $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
