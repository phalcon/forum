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

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

/**
 * Phosphorum\Frontend\Providers\ViewProvider
 *
 * @package Phosphorum\Frontend\Providers
 */
class ViewProvider implements ServiceProviderInterface
{
    protected $viewsDir;

    /**
     * ViewProvider constructor.
     *
     * @param string $viewsDir
     */
    public function __construct(string $viewsDir)
    {
        $this->viewsDir = $viewsDir;
    }

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $view = $container->get('view');

        $view->setViewsDir($this->viewsDir);
    }
}
