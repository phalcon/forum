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

namespace Phosphorum\Providers\VoltTemplate;

use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ViewBaseInterface;
use Phosphorum\Providers\Abstrakt;

/**
 * Phosphorum\Providers\VoltTemplate\ServiceProvider
 *
 * @package Phosphorum\Providers\VoltTemplate
 */
class ServiceProvider extends Abstrakt
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
        $this->di->setShared(
            $this->serviceName,
            function (ViewBaseInterface $view, DiInterface $di = null) {
                $config = container('config')->volt;

                $volt = new Volt($view, $di ?: container());

                $volt->setOptions(
                    [
                        'compiledPath' => function ($templatePath) use ($config) {
                            $filename = str_replace(
                                ['\\', '/'],
                                $config->separator,
                                trim(substr($templatePath, strlen(BASE_DIR)), '\\/')
                            );

                            $filename = basename($filename, '.volt') . $config->compiledExt;
                            $cacheDir = rtrim($config->cacheDir, '\\/') . DIRECTORY_SEPARATOR;

                            if (!is_dir($cacheDir)) {
                                @mkdir($cacheDir, 0755, true);
                            }

                            return rtrim($config->cacheDir, '\\/') . DIRECTORY_SEPARATOR . $filename;
                        },
                        'compileAlways' => (bool) $config->forceCompile,
                    ]
                );

                $volt->getCompiler()->addFunction('number_format', function ($resolvedArgs) {
                    return 'number_format(' . $resolvedArgs . ')';
                })->addFunction('chr', function ($resolvedArgs) {
                    return 'chr(' . $resolvedArgs . ')';
                });

                return $volt;
            }
        );
    }
}
