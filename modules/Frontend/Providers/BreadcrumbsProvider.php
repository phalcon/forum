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

use Phalcon\Breadcrumbs;
use Phalcon\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

/**
 * Phosphorum\Frontend\Providers\BreadcrumbsProvider
 *
 * @package Phosphorum\Frontend\Providers
 */
class BreadcrumbsProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $service = function () use ($container) {
            $breadcrumbs = new Breadcrumbs();
            $breadcrumbs->setEventsManager($container->get('eventsManager'));
            $breadcrumbs->setSeparator('');

            return $breadcrumbs;
        };

        $container->setShared('breadcrumbs', $service);
    }
}
