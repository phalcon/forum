<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Provider\Environment;

use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Environment\ServiceProvider
 *
 * @package Phosphorum\Provider\Environment
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'environment';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->set(
            $this->serviceName,
            function ($value = null) {
                $environment = container('bootstrap')->getEnvironment();

                if (func_num_args() > 0) {
                    $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

                    foreach ($patterns as $pattern) {
                        if ($pattern === $environment) {
                            return true;
                        }
                    }

                    return false;
                }

                return $environment;
            }
        );
    }
}
