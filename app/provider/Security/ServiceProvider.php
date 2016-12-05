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

namespace Phosphorum\Provider\Security;

use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Security\ServiceProvider
 *
 * @package Phosphorum\Provider\Security
 */
class ServiceProvider extends AbstractServiceProvider
{
    const DEFAULT_WORK_FACTOR = 12;

    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'security';

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $this->di->setShared(
            $this->serviceName,
            function () {
                $config = container('config');
                $security = new Security();

                $workFactor = self::DEFAULT_WORK_FACTOR;
                if (!empty($config->application->hashingFactor)) {
                    $workFactor = (int) $config->application->hashingFactor;
                }

                $security->setWorkFactor($workFactor);

                return $security;
            }
        );
    }
}
