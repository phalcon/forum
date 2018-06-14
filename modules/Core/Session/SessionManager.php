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

namespace Phosphorum\Core\Session;

use Phalcon\Config;
use Phalcon\Session\AdapterInterface;

/**
 * Phosphorum\Core\Session\SessionManager
 *
 * @package Phosphorum\Core\Session
 */
class SessionManager
{
    /**
     * Creates the application session instance.
     *
     * @param Config   $config
     *
     * @return AdapterInterface
     */
    public function create(Config $config): AdapterInterface
    {
        $default = $config->get('default', 'files');
        $possibleAdaper = $config->get('adapter');

        if ($possibleAdaper != null) {
            $adapter = $possibleAdaper;
        } else {
            $adapter = sprintf('\Phalcon\Session\Adapter\%s', ucfirst($default));
        }

        return new $adapter(
            $this->createConfig($config, $default)
        );
    }

    /**
     * Creates session adapter configuration.
     *
     * @param  Config $commonConfig
     * @param  string $driverName
     *
     * @return array
     */
    protected function createConfig(Config $commonConfig, string $driverName): array
    {
        $driver = $commonConfig->path('drivers.' . $driverName);

        $defaults = [
            'prefix'   => $commonConfig->get('prefix'),
            'uniqueId' => $commonConfig->get('uniqueId'),
            'lifetime' => $commonConfig->get('lifetime'),
        ];

        return array_merge($driver->toArray(), $defaults);
    }
}
