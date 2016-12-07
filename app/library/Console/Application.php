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

namespace Phosphorum\Console;

use Phalcon\Cli\Console;
use Phalcon\DiInterface;
use Phosphorum\Listener\CliInputListener;

/**
 * Phosphorum\Console\Application
 *
 * @package Phosphorum\Console
 */
class Application extends Console
{
    /**
     * The command line argument list.
     * @var array
     */
    protected $arguments = [];

    /**
     * Application constructor.
     *
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        parent::__construct($di);

        $this->arguments = $_SERVER["argv"];

        $this->setUpListeners();
    }

    /**
     * Gets the command line argument list.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setting up application listeners
     */
    protected function setUpListeners()
    {
        container('eventsManager')->attach('console', new CliInputListener());
    }
}
