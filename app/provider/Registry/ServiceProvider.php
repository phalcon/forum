<?php

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

namespace Phosphorum\Provider\Registry;

use Phosphorum\Provider\AbstractServiceProvider;
use Phalcon\Registry;

/**
 * Phosphorum\Provider\Registry\ServiceProvider
 *
 * @package Phosphorum\Provider\Registry
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string $serviceName
     */
    protected $serviceName = 'registry';

    /**
     * Pathes should be added to registry
     * @var array $path
     */
    protected $path = [
        'public_path' => BASE_PATH . '/public/'
    ];

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $path = $this->path;

        $this->di->setShared(
            $this->serviceName,
            function () use ($path) {
                $registry = new Registry();
                foreach ($path as $offset => $value) {
                    $registry->offsetSet($offset, $value);
                }

                return $registry;
            }
        );
    }
}
