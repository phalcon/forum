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

namespace Phosphorum\Provider\Mailer;

use Phalcon\Mailer\Manager;
use InvalidArgumentException;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Mail\ServiceProvider
 *
 * @package Phosphorum\Provider\Mailer
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'mailer';

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
                /** @var \Phalcon\Config $config */
                $config = container('config')->mailer;
                $driver = $config->get('driver');

                switch ($driver) {
                    case 'smtp':
                    case 'mail':
                    case 'sendmail':
                        $mailerConfig = $config->toArray();

                        $manager = new Manager($mailerConfig);
                        $manager->setDI(container());

                        return $manager;
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid mail driver. Expected either "smtp" or "mail" or "sendmail". Got "%s".',
                        is_scalar($driver) ? $driver : var_export($driver, true)
                    )
                );
            }
        );
    }
}
