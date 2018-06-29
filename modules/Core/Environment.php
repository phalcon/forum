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

namespace Phosphorum\Core;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;
use Phalcon\Registry;
use Phosphorum\Core\Exceptions\InvalidArgumentException;

/**
 * Phosphorum\Core\Environment
 *
 * @package Phosphorum\Core
 */
final class Environment implements InjectionAwareInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    const PRODUCTION  = 0x10;
    const STAGING     = 0x20;
    const TESTING     = 0x30;
    const DEVELOPMENT = 0x40;

    /**
     * Internal application registry.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * The base path for the application.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The environment stage name.
     *
     * @var int
     */
    protected $stage;

    /**
     * RegistryProvider constructor.
     *
     * @param string           $basePath
     * @param DiInterface|null $container
     */
    public function __construct(string $basePath, DiInterface $container = null)
    {
        $this->__DiInject($container);

        $this->registry = $this->getDI()->get(Registry::class);
        $this->stage = Environment::DEVELOPMENT;

        $this->setBasePath($basePath ?? dirname(dirname(__DIR__)));
    }

    /**
     * Set the base path for the forum installation.
     *
     * @param  string $basePath
     *
     * @return $this
     */
    public function setBasePath(string $basePath): Environment
    {
        $this->basePath = rtrim($basePath, '\\/');

        $this->registerPaths();

        return $this;
    }

    /**
     * Initialize all of the application paths in the DI container.
     *
     * @return void
     */
    protected function registerPaths(): void
    {
        $this->registry->offsetSet('paths', (object) [
            'base'    => $this->getPath(),
            'public'  => $this->getPath('public'),
            'modules' => $this->getPath('modules'),
            'storage' => $this->getPath('storage'),
            'config'  => $this->getPath('config'),
            'cache'   => $this->getPath('storage' . DIRECTORY_SEPARATOR . 'cache'),
        ]);
    }

    /**
     * Get the path to the forum installation.
     *
     * @param  string $path
     * @return string
     */
    public function getPath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the base configuration folder.
     *
     * @return string
     */
    public function getConfigBasePath(): string
    {
        return $this->registry->offsetGet('paths')->config;
    }

    /**
     * Get the path to the cached config.php file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->registry->offsetGet('paths')->cache . '/application/config.php';
    }

    /**
     * Get the path to the cached config.php file.
     *
     * @return string
     */
    public function getVoltCachePath(): string
    {
        return $this->registry->offsetGet('paths')->cache . '/volt';
    }

    /**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function isConfigurationCached(): bool
    {
        return file_exists($this->getCachedConfigPath());
    }

    /**
     * Sets current application stage.
     *
     * @param  int $stage
     *
     * @throes InvalidArgumentException
     */
    public function setStage(int $stage): void
    {
        if ($stage > Environment::DEVELOPMENT || $stage < Environment::PRODUCTION) {
            throw new InvalidArgumentException(
                sprintf('Application stage "%d" is not valid.', $stage)
            );
        }

        $this->stage = $stage;
    }

    /**
     * Checks current application stage.
     *
     * @return bool
     */
    public function isCurrentStage(): bool
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            foreach ($patterns as $pattern) {
                if ($pattern === $this->stage) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Sets current application stage.
     *
     * @return int
     */
    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * Determine if PHP is being run from the CLI.
     *
     * @return bool
     */
    public function isCommandLineInterface(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Checks if currently running under MS Windows.
     *
     * @return bool
     */
    public function isWindows(): bool
    {
        return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));
    }
}
