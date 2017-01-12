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

namespace Phosphorum\Provider\FileSystem;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\FileSystem\ServiceProvider
 *
 * @package Phosphorum\Provider\FileSystem
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'filesystem';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->set(
            $this->serviceName,
            function ($root = null) {
                if ($root === null) {
                    $root = dirname(app_path());
                }

                return new Filesystem(new Local($root));
            }
        );
    }
}
