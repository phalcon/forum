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

namespace Phosphorum\Core\Mvc\View\Engine;

use Closure;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phosphorum\Core\Environment;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Di\InjectionAwareInterface;
use Phosphorum\Core\Traits\InjectionAwareTrait;
use Phalcon\Mvc\ViewBaseInterface;

/**
 * Phosphorum\Core\Mvc\View\Engine\VoltManager
 *
 * @package Phosphorum\Core\Mvc\View\Engine
 */
final class VoltManager implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Creates the Volt Template Engine.
     *
     * @param Environment       $env
     * @param Config            $config
     * @param ViewBaseInterface $view
     * @param DiInterface|null  $container
     *
     * @return Volt
     */
    public function create(
        Environment $env,
        Config $config,
        ViewBaseInterface $view,
        DiInterface $container = null
    ): Volt {
        $volt = new Volt($view, $container ?: $this->getDI());

        $compiledPath = $this->createCompiledPathName($env);
        $isCompileAlways = $this->isCompileAlways($env, $config);

        $this->setVoltOptions($volt, $compiledPath, $isCompileAlways);
        $this->registerVoltExtensions($volt, [
            new VoltFunctions($this->getDI()),
        ]);

        return $volt;
    }

    /**
     * Set Volt's options
     *
     * @param Volt    $volt
     * @param Closure $compiledPath
     * @param bool    $compileAlways
     */
    protected function setVoltOptions(Volt $volt, Closure $compiledPath, bool $compileAlways): void
    {
        $volt->setOptions(
            [
                'compiledPath'  => $compiledPath,
                'compileAlways' => $compileAlways,
            ]
        );
    }

    /**
     * Creates a writable path where the compiled PHP templates will be placed.
     *
     * @param  Environment $env
     *
     * @return Closure
     */
    protected function createCompiledPathName(Environment $env): Closure
    {
        return function ($path) use ($env) {
            $path     = trim(substr($path, strlen($env->getPath())), '\\/');
            $filename = basename(str_replace(['\\', '/'], '_', $path), '.volt') . '.php';
            $cacheDir = $env->getVoltCachePath();

            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }

            return $cacheDir . DIRECTORY_SEPARATOR . $filename;
        };
    }

    /**
     * Tell Volt if the templates must be compiled in each request or only when they change.
     *
     * @param  Environment $env
     * @param  Config      $config
     *
     * @return bool
     */
    protected function isCompileAlways(Environment $env, Config $config): bool
    {
        $isDevelopmentStage = $env->isCurrentStage(Environment::DEVELOPMENT);

        return ($isDevelopmentStage || $config->get('debug', false));
    }

    /**
     * Registers Volt's extensions.
     *
     * @param  Volt  $volt
     * @param  array $extensions
     *
     * @return void
     */
    protected function registerVoltExtensions(Volt $volt, array $extensions): void
    {
        foreach ($extensions as $extension) {
            $volt->getCompiler()->addExtension($extension);
        }
    }
}
