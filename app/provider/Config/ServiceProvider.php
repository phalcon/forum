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

namespace Phosphorum\Provider\Config;

use RuntimeException;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Config\ServiceProvider
 *
 * @package Phosphorum\Provider\Config
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'config';

    /**
     * Config files.
     * @var array
     */
    protected $configs = [
        'logger',
        'cache',
        'session',
        'database',
        'metadata',
        'queue',
        'devtools',
        'annotations',
        'email',
        'mailer',
        'patreon',
        'config',
    ];

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function boot()
    {
        $configPath = config_path('config.php');

        if (!file_exists($configPath) || !is_file($configPath)) {
            throw new RuntimeException(
                sprintf(
                    'The application config not found. Please make sure that the file "%s" is present',
                    $configPath
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $configs = $this->configs;

        $this->di->setShared(
            $this->serviceName,
            function () use ($configs) {
                return Factory::create($configs);
            }
        );
    }
}
