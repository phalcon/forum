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

namespace Phosphorum\Utils;

use Phalcon\Config;
use Dropbox\Client;
use Phalcon\DI\Injectable;
use League\Flysystem\Filesystem;
use League\Flysystem\Dropbox\DropboxAdapter;

/**
 * Backup
 *
 * Backups the default database to Dropbox (only MySQL/Unix)
 * @property \Phalcon\Config config
 */
class Backup extends Injectable
{
    public function generate()
    {
        if (PHP_SAPI != 'cli') {
            throw new \Exception("This script only can be used in CLI");
        }

        $config = $this->config->get('database');

        system(sprintf(
            '/usr/bin/mysqldump -u %s -h %s -p%s -r /tmp/phosphorum.sql %s',
            $config->username,
            $config->host,
            $config->password,
            $config->dbname
        ));
        system('bzip2 -f /tmp/phosphorum.sql');

        $config = $this->config->get('dropbox');

        if (!$config instanceof Config) {
            throw new \Exception("Unable to retrieve Dropbox credentials. Please check Forum Configuration");
        }

        if (!$config->get('appSecret') || !$config->get('accessToken')) {
            throw new \Exception("Please provide correct 'appSecret' and 'accessToken' config values");
        }

        $sourcePath = '/tmp/phosphorum.sql.bz2';
        if (!file_exists($sourcePath)) {
            throw new \Exception("Backup could not be created");
        }

        $client = new Client($config->get('accessToken'), $config->get('appSecret'));
        $adapter = new DropboxAdapter($client, $config->get('prefix', null));
        $filesystem = new Filesystem($adapter);

        $dropboxPath = '/phosphorum.sql.bz2';

        if ($filesystem->has($dropboxPath)) {
            $filesystem->delete($dropboxPath);
        }

        $fp = fopen($sourcePath, "rb");
        $filesystem->putStream($dropboxPath, $fp);
        fclose($fp);

        @unlink($sourcePath);
    }
}
