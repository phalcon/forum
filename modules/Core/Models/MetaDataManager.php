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

namespace Phosphorum\Core\Models;

use Phalcon\Config;
use Phosphorum\Core\Environment;
use Phalcon\Mvc\Model\MetaDataInterface;
use Phosphorum\Core\Exceptions\DomainException;
use Phosphorum\Core\Exceptions\InvalidArgumentException;

/**
 * Phosphorum\Core\Models\MetaDataManager
 *
 * @package Phosphorum\Core\Models
 */
final class MetaDataManager
{
    /**
     * Creates the application metadata instance.
     *
     * @param  Environment $env
     * @param  Config      $config
     *
     * @return MetaDataInterface
     */
    public function create(Environment $env, Config $config): MetaDataInterface
    {
        $default = $config->get('default', 'files');
        $possibleAdaper = $config->path(sprintf('drivers.%s.adapter', $default));

        if ($possibleAdaper != null) {
            $adapter = $possibleAdaper;
        } else {
            $adapter = sprintf('\Phalcon\Mvc\Model\Metadata\%s', ucfirst($default));
        }

        return new $adapter(
            $this->createConfig($env, $config, $default)
        );
    }

    /**
     * Creates a models metadata adapter configuration.
     *
     * @param  Environment $env
     * @param  Config      $commonConfig
     * @param  string      $driverName
     *
     * @return array
     */
    protected function createConfig(Environment $env, Config $commonConfig, string $driverName): array
    {
        $driver = $commonConfig->path(sprintf('drivers.%s', $driverName));
        if ($driver instanceof Config == false) {
            $driver = new Config();
        }

        $defaults = [
            'prefix'   => $commonConfig->get('prefix'),
            'lifetime' => $commonConfig->get('lifetime'),
        ];

        $merged = $driver->merge(new Config($defaults));

        $this->resolveMetaDataDir($env, $merged);

        return $merged;
    }

    /**
     * Resolve MetaData path (make it absolute) if needed.
     *
     * @param  Environment $env
     * @param  Config      $config
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    protected function resolveMetaDataDir(Environment $env, Config $config): void
    {
        if ($config->offsetExists('metaDataDir') == false) {
            return;
        }

        $path = $config->get('metaDataDir');

        if (is_string($path) == false) {
            throw new InvalidArgumentException(
                sprintf(
                    'The metaDataDir parameter must be a string, got %s.',
                    gettype($path)
                )
            );
        }

        if (ctype_print($path) == false) {
            throw new DomainException(
                'The metaDataDir can not have non-printable characters or be empty.'
            );
        }

        // Looks like it is relative path
        if ($path[0] !== DIRECTORY_SEPARATOR && preg_match('#\A[A-Z]:(?![^/\\\\])#i', $path) > 0) {
            $config->offsetSet('metaDataDir', $env->getPath($path));
        }
    }
}
