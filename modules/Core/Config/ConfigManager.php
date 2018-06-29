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

namespace Phosphorum\Core\Config;

use DirectoryIterator;
use Phalcon\Config;
use Phalcon\Config\Factory;
use Phalcon\DiInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;
use Phosphorum\Core\Environment;

/**
 * Phosphorum\Core\Config\ConfigManager
 *
 * @package Phosphorum\Core\Config
 */
class ConfigManager
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /**
     * The application environment.
     *
     * @var Environment
     */
    protected $env;

    /**
     * Allowed configuration extensions.
     *
     * @var array
     */
    protected $allowedExtensions = ['php', 'json', 'yml', 'yaml', 'ini'];

    /**
     * Config Manager constructor.
     *
     * @param DiInterface|null $container
     */
    public function __construct(DiInterface $container = null)
    {
        $this->__DiInject($container);
        $this->env = $this->getDI()->get(Environment::class);
    }

    /**
     * Creates the application configuration instance.
     *
     * @return Config
     */
    public function create(): Config
    {
        if ($this->env->isCurrentStage(Environment::DEVELOPMENT)) {
            return $this->loadConfiguration();
        }

        if ($this->env->isConfigurationCached()) {
            /** @noinspection PhpIncludeInspection */
            return new Config(require $this->env->getCachedConfigPath());
        }

        $config = $this->loadConfiguration();
        $this->refreshCache($config);

        return $config;
    }

    /**
     * Load application configuration from all files.
     *
     * @return Config
     */
    protected function loadConfiguration(): Config
    {
        $config = new Config();
        $configFiles = $this->scanForConfiguration();

        foreach ($configFiles as $options) {
            $configPart = Factory::load($options);
            $configName = $options['basename'];

            if (!$config->offsetExists($configName) || !$config->{$configName} instanceof Config) {
                $config->offsetSet($configName, new Config());
            }

            $config->get($configName)->merge($configPart);
        }

        return $config;
    }

    /**
     * Scan all configuration files ready to be used.
     *
     * @return array
     */
    protected function scanForConfiguration(): array
    {
        $files = [];
        $configDirectory = new DirectoryIterator($this->env->getConfigBasePath());

        foreach ($configDirectory as $fileInfo) {
            $ext = strtolower($fileInfo->getExtension());
            if (in_array($ext, $this->allowedExtensions, true) == false) {
                continue;
            }

            $files[] = [
                'filePath' => $fileInfo->getPathname(),
                'basename' => $fileInfo->getBasename('.' . $fileInfo->getExtension()),
                'adapter'  => $ext,
            ];
        }

        if (empty($files) == false) {
            return $files;
        }

        foreach ($configDirectory as $fileInfo) {
            $ext = strtolower($fileInfo->getExtension());
            if ($ext !== 'dist') {
                continue;
            }

            $realExt = explode('.', substr($fileInfo->getFilename(), 0, -5), 2);
            if (isset($realExt[1]) == false ||
                in_array(strtolower($realExt[1]), $this->allowedExtensions, true) == false
            ) {
                continue;
            }

            copy($fileInfo->getPathname(), substr($fileInfo->getPathname(), 0, -5));

            $files[] = [
                'filePath' => $fileInfo->getPathname(),
                'basename' => $fileInfo->getBasename('.' . $fileInfo->getExtension() . '.dist'),
                'adapter'  => strtolower($realExt[1]),
            ];
        }

        return $files;
    }

    /**
     * Save config file into cached config file.
     *
     * @param  Config $config
     * @return void
     */
    public function refreshCache(Config $config): void
    {
        file_put_contents($this->env->getCachedConfigPath(), $this->dumpConfiguration($config));
    }

    /**
     * Dump configuration to file ready representation.
     *
     * @param  Config $config
     * @return string
     */
    protected function dumpConfiguration(Config $config): string
    {
        $data = $config->toArray();

        $configText = var_export($data, true);
        $headerText = '<?php
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

/**
 * WARNING
 *
 * Manual changes to this file may cause a malfunction of the system.
 * Be careful when changing settings!
 *
 * Generated at: %DATE%
 */
return ';

        return str_replace('%DATE%', date('r'), $headerText) . $configText . ';';
    }
}
