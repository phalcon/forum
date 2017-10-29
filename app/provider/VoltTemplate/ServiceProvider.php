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

namespace Phosphorum\Provider\VoltTemplate;

use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ViewBaseInterface;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\VoltTemplate\ServiceProvider
 *
 * @package Phosphorum\Provider\VoltTemplate
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'volt';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $service = function (ViewBaseInterface $view, DiInterface $di = null) {
            $volt = new Volt($view, $di ?: container());

            $volt->setOptions(
                [
                    'compiledPath'  => function ($path) {
                        $path     = trim(substr($path, strlen(dirname(app_path()))), '\\/');
                        $filename = basename(str_replace(['\\', '/'], '_', $path), '.volt') . '.php';
                        $cacheDir = cache_path('volt');

                        if (!is_dir($cacheDir)) {
                            @mkdir($cacheDir, 0755, true);
                        }

                        return $cacheDir . DIRECTORY_SEPARATOR . $filename;
                    },
                    'compileAlways' => environment('development') || env('APP_DEBUG', false),
                ]
            );

            $volt->getCompiler()->addExtension(new VoltFunctions());

            return $volt;
        };

        $this->di->setShared($this->serviceName, $service);
    }
}
