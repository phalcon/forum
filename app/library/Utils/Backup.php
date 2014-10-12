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

namespace Phosphorum\Utils;

use Dropbox\AuthInfo;
use Dropbox\Client;
use Dropbox\Path;
use Dropbox\WriteMode;
use Phalcon\DI\Injectable;

/**
 * Backup
 *
 * Backups the default database to Dropbox (only MySQL/Unix)
 */
class Backup extends Injectable
{
    public function generate()
    {
        if (PHP_SAPI != 'cli') {
            throw new \Exception("This script only can be used in CLI");
        }

        $config = $this->config->database;
        system('/usr/bin/mysqldump -u ' . $config->username . ' -p' . $config->password . ' -r /tmp/phosphorum.sql ' . $config->dbname);
        system('bzip2 /tmp/phosphorum.sql');

        $sourcePath = '/tmp/phosphorum.sql.bz2';
        if (!file_exists($sourcePath)) {
            throw new \Exception("Backup could not be created");
        }

        list($accessToken, $host) = AuthInfo::loadFromJsonFile(APP_PATH . '/app/config/backup.auth');

        $client = new Client($accessToken, "phosphorum", null, $host);

        $dropboxPath = '/phosphorum.sql.bz2';

        $pathError = Path::findErrorNonRoot($dropboxPath);
        if ($pathError !== null) {
            throw new \Exception("Invalid <dropbox-path>: $pathError");
        }

        try {
            $client->delete($dropboxPath);
        } catch (\Exception $e) {
            // ...
        }

        $size = null;
        if (\stream_is_local($sourcePath)) {
            $size = \filesize($sourcePath);
        }

        $fp = fopen($sourcePath, "rb");
        $metadata = $client->uploadFile($dropboxPath, WriteMode::add(), $fp, $size);
        fclose($fp);

        //print_r($metadata);
        @unlink($sourcePath);
    }
}
