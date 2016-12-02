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

use Phalcon\Di;

if (!function_exists('app_path')) {
    /**
     * Get the application path.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('cache_path')) {
    /**
     * Get the cache path.
     *
     * @param  string $path
     * @return string
     */
    function cache_path($path = '')
    {
        return app_path('cache') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app_path('config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'empty':
                return '';
            case 'null':
                return null;
        }
        return $value;
    }
}

if (!function_exists('container')) {
    /**
     * Calls the default Dependency Injection container.
     *
     * @param  mixed
     * @return mixed|\Phalcon\DiInterface
     */
    function container()
    {
        $default = Di::getDefault();
        $args = func_get_args();

        if (empty($args)) {
            return $default;
        }

        return call_user_func_array([$default, 'get'], $args);
    }
}


if (!function_exists('hash_equals')) {
    /**
     * Timing attack safe string comparison.
     * Support for PHP 5.5
     *
     * @param  string $knownString The string of known length to compare against
     * @param  string $userString  The user-supplied string
     * @return bool
     */
    function hash_equals($knownString, $userString)
    {
        if (!is_string($knownString) || !is_string($userString)) {
            return false;
        }

        if (strlen($knownString) !== strlen($userString)) {
            return false;
        }

        $res = $knownString ^ $userString;
        $ret = 0;

        for ($i = strlen($res) - 1; $i >= 0; $i--) {
            $ret |= ord($res[$i]);
        }

        return !$ret;
    }
}
