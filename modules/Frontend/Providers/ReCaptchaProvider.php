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

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phosphorum\Frontend\ReCaptcha\ReCaptchaManager;

/**
 * Phosphorum\Frontend\Providers\ReCaptchaProvider
 *
 * @package Phosphorum\Frontend\Providers
 */
class ReCaptchaProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            $reCaptcha = new ReCaptchaManager(
                $container->get(Config::class)->get('recaptcha', new Config()),
                $container->get('tag')
            );

            return $reCaptcha;
        };

        $container->setShared('recaptcha', $service);
    }
}
