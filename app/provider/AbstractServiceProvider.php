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

namespace Phosphorum\Provider;

use LogicException;
use Phalcon\DiInterface;
use Phalcon\Mvc\User\Component;

/**
 * Phosphorum\Provider\AbstractServiceProvider
 *
 * @package Phosphorum\Provider
 */
abstract class AbstractServiceProvider extends Component implements ServiceProviderInterface
{
    /**
     * The Service name.
     * @var string
     */
    protected $serviceName;

    final public function __construct(DiInterface $di)
    {
        if (!$this->serviceName) {
            throw new LogicException(
                sprintf('The service provider defined in "%s" cannot have an empty name.', get_class($this))
            );
        }

        $this->setDI($di);

        $this->configure();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return $this->serviceName;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function configure()
    {
    }
}
