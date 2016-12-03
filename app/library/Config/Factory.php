<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Config;

use Phalcon\Config;

/**
 * \Phosphorum\Config\Factory
 *
 * @package Phosphorum\Config
 */
class Factory
{
    /**
     * @todo This will be removed in the near future.
     * Now it should be here temporarily.
     * @var array
     */
    protected static $providers = [
        'cache',
        'config',
    ];

    public static function create()
    {
        $default = new Config();

        foreach (self::$providers as $provider) {
            $configPath = config_path($provider) . '.php';

            if (!is_readable($configPath) || !is_file($configPath)) {
                trigger_error('Unable to read config from ' . $configPath, E_USER_WARNING);
                continue;
            }

            /** @noinspection PhpIncludeInspection */
            $config = include $configPath;

            if (!$config instanceof Config && !is_array($config)) {
                $type = gettype($config);
                if ($type == 'boolean') {
                    $type .= ($type ? ' (true)' : ' (false)');
                } elseif (is_object($type)) {
                    $type = get_class($type);
                }

                trigger_error(
                    sprintf(
                        'Unable to load config file. Config must be either an array or %s instance. Got %s',
                        Config::class,
                        $type
                    ),
                    E_USER_WARNING
                );

                continue;
            }

            if (is_array($config)) {
                $config = new Config($config);
            }

            $default->merge($config);
        }

        $overridePath = config_path(APPLICATION_ENV) . '.php';

        if (is_file($overridePath) && is_readable($overridePath)) {
            /** @noinspection PhpIncludeInspection */
            $override = include $overridePath;

            if (is_array($override)) {
                $override = new Config($override);
            }

            if ($override instanceof Config) {
                $default->merge($override);
            }
        }

        return $default;
    }
}
