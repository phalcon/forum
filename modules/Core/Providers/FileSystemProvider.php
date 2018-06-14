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

namespace Phosphorum\Core\Providers;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phosphorum\Core\Environment;

/**
 * Phosphorum\Core\Providers\FileSystemProvider
 *
 * @package Phosphorum\Core\Providers
 */
class FileSystemProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->set(FilesystemInterface::class, $service);
    }

    protected function createService(DiInterface $container)
    {
        return function (string $baseDirectory = null) use ($container) {
            if ($baseDirectory === null) {
                /** @var Environment $environment */
                $environment = $container->get(Environment::class);
                $baseDirectory = $environment->getPath();
            }

            return new Filesystem(
                new Local($baseDirectory)
            );
        };
    }
}
