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

namespace Phosphorum\Provider\consoleOutput;

use League\CLImate\CLImate;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\consoleOutput\ServiceProvider
 *
 * @package Phosphorum\Provider\consoleOutput
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string $serviceName
     */
    protected $serviceName = 'consoleOutput';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared($this->serviceName, CLImate::class);
    }
}
