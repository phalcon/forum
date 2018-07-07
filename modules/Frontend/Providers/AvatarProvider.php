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

namespace Phosphorum\Frontend\Providers;

use Closure;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Avatar\Gravatar;
use Phalcon\Avatar\Avatarable;
use Phalcon\Di\ServiceProviderInterface;

/**
 * Phosphorum\Frontend\Providers\AvatarProvider
 *
 * @package Phosphorum\Frontend\Providers
 */
class AvatarProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = $this->createService($container);

        $container->setShared(Avatarable::class, $service);
    }

    protected function createService(DiInterface $container): Closure
    {
        return function () use ($container) {
            /** @var Config $config */
            $config = $container->get(Config::class);

            $avatarConfig = $config->get('avatar');
            if ($avatarConfig instanceof Config == false) {
                $avatarConfig = new Config();
            }

            return new Gravatar($avatarConfig);
        };
    }
}
