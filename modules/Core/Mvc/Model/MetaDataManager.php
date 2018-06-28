<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Mvc\Model;

use Phalcon\Config;
use Phalcon\Mvc\Model\MetaDataInterface;

/**
 * Phosphorum\Core\Mvc\Model\MetaDataManager
 *
 * @package Phosphorum\Core\Mvc\Model
 */
final class MetaDataManager
{
    /**
     * Creates the application metadata instance.
     *
     * @param  Config $config
     *
     * @return MetaDataInterface
     */
    public function create(Config $config): MetaDataInterface
    {
        $default = $config->get('default', 'files');
        $possibleAdaper = $config->path(sprintf('drivers.%s.adapter', $default));

        if ($possibleAdaper != null) {
            $adapter = $possibleAdaper;
        } else {
            $adapter = sprintf('\Phalcon\Mvc\Model\Metadata\%s', ucfirst($default));
        }

        return new $adapter(
            $this->createConfig($config, $default)
        );
    }

    /**
     * Creates a models metadata configuration.
     *
     * @param  Config $config
     * @param  string $driverName
     *
     * @return array
     */
    protected function createConfig(Config $config, string $driverName): array
    {
        $driver = $config->path(sprintf('drivers.%s', $driverName));
        if ($driver instanceof Config == false) {
            $driver = new Config();
        }

        $defaults = [
            'prefix'   => $config->get('prefix', ''),
            'lifetime' => $config->get('lifetime', 0),
        ];

        return array_merge($driver->toArray(), $defaults);
    }
}
