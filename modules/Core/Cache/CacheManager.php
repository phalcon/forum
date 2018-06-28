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

namespace Phosphorum\Core\Cache;

use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Config;

/**
 * Phosphorum\Core\Cache\CacheManager
 *
 * @package Phosphorum\Core\Cache
 */
final class CacheManager
{
    /**
     * Creates the models cache instance.
     *
     * @param  Config $config
     *
     * @return BackendInterface
     */
    public function create(Config $config): BackendInterface
    {
        $default = $config->get('default', 'files');
        $possibleAdaper = $config->path(sprintf('drivers.%s.adapter', $default));

        if ($possibleAdaper != null) {
            $adapter = $possibleAdaper;
        } else {
            $adapter = sprintf('\Phalcon\Cache\Backend\%s', ucfirst($default));
        }

        return new $adapter(
            new Data(['lifetime' => $config->get('lifetime', 0)]),
            $this->createConfig($config, $default)
        );
    }

    /**
     * Creates a models cache configuration.
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

        $driver->offsetUnset('adapter');

        $defaults = [
            'statsKey' => 'SMC:' . substr(md5($config->get('prefix', '')), 0, 16) . '_',
            'prefix' => 'PMC_'.$config->get('prefix', ''),
        ];

        return array_merge($driver->toArray(), $defaults);
    }
}
