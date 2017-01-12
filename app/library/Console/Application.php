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

use Phosphorum\Version;
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
     * The raw command line argument list.
     * @var array
     */
    protected $rawArguments = [];

    /**
     * Application constructor.
     *
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        parent::__construct($di);

        $this->rawArguments = $_SERVER["argv"];

        $this->setUpListeners();
    }

    /**
     * Get the application name.
     *
     * @return string
     */
    public function getName()
    {
        return container('config')->site->software;
    }

    /**
     * Ghe application version.
     *
     * @return string
     */
    public function getVersion()
    {
        return Version::get();
    }

    /**
     * Gets the raw command line argument list.
     *
     * @return array
     */
    public function getRawArguments()
    {
        return $this->rawArguments;
    }

    /**
     * Set the cleaned command line arguments.
     *
     * @param  array $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->_arguments = $arguments;

        return $this;
    }

    /**
     * Setting up application listeners
     */
    protected function setUpListeners()
    {
        container('eventsManager')->attach('console', new CliInputListener());
    }
}
