<?php

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

namespace Phosphorum\Provider\Registry;

use Phalcon\Text;
use Phalcon\Registry;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Registry\ServiceProvider
 *
 * @package Phosphorum\Provider\Registry
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string $serviceName
     */
    protected $serviceName = 'registry';

    /**
     * Data should be added to registry
     * @var array $data
     */
    protected $data = [
        'paths' => [
            'basePath' => '/',
            'public' => 'public',
            'assets' => 'public/assets',
            'storage' => 'storage',
            'logs' => 'storage/logs',
            'pids' => 'storage/pids',
            'annotationsCache' => 'storage/cache/annotations',
            'configCache' => 'storage/cache/config',
            'dataCache' => 'storage/cache/data',
            'metaDataCache' => 'storage/cache/metaData',
            'viewsCache' => 'storage/cache/views',
            'voltCache' => 'storage/cache/volt',
        ],
    ];

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $data = $this->data;

        $this->di->setShared(
            $this->serviceName,
            function () use ($data) {
                $registry = new Registry();

                $registry->offsetSet('paths', (object) array_map(function ($path) {
                    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
                    $newPath = BASE_PATH . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;

                    return Text::reduceSlashes($newPath);
                }, $data['paths']));

                return $registry;
            }
        );
    }
}
