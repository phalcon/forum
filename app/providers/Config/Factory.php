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

namespace Phosphorum\Providers\Config;

use Phalcon\Config;

/**
 * Phosphorum\Providers\Config\Factory
 *
 * @package Phosphorum\Providers\Config
 */
class Factory
{
    public static function create(array $providers = [])
    {
        $default = new Config();
        $merge = self::merge();

        foreach ($providers as $provider) {
            $merge($default, config_path("$provider.php"), $provider == 'config' ? null : $provider);
        }

        $overridePath = config_path(APPLICATION_ENV) . '.php';
        if (APPLICATION_ENV !== ENV_PRODUCTION) {
            $merge($default, $overridePath);
        }

        return $default;
    }

    protected static function merge()
    {
        return function (Config &$config, $path, $name = null) {
            if (file_exists($path)) {
                /** @noinspection PhpIncludeInspection */
                $toMerge = require $path;

                if (is_array($toMerge)) {
                    $toMerge = new Config($toMerge);
                }

                if ($toMerge instanceof Config) {
                    if ($name) {
                        if (!$config->offsetExists($name) || !$config->{$name} instanceof Config) {
                            $config->offsetSet($name, new Config());
                        }

                        $config->get($name)->merge($toMerge);
                    } else {
                        $config->merge($toMerge);
                    }
                }
            }
        };
    }
}
