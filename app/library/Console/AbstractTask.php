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

use Phalcon\Di\Injectable;
use Phalcon\Cli\Console\Exception;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\Line;
use Phalcon\Logger\AdapterInterface;

/**
 * Phosphorum\Console\AbstractTask
 *
 * @package Phosphorum\Console
 *
 * @method AdapterInterface critical(string $message, array $context = null)
 * @method AdapterInterface emergency(string $message, array $context = null)
 * @method AdapterInterface debug(string $message, array $context = null)
 * @method AdapterInterface error(string $message, array $context = null)
 * @method AdapterInterface info(string $message, array $context = null)
 * @method AdapterInterface warning(string $message, array $context = null)
 * @method AdapterInterface notice(string $message, array $context = null)
 * @method AdapterInterface alert(string $message, array $context = null)
 */
class AbstractTask extends Injectable implements TaskInterface
{
    /**
     * Current output stream.
     * @var Stream
     */
    protected $output;

    /**
     * AbstractTask constructor.
     */
    final public function __construct()
    {
        if (method_exists($this, 'onConstruct')) {
            $this->{"onConstruct"}();
        }

        $this->setUp();
    }

    /**
     * Setting up concrete task.
     */
    private function setUp()
    {
        $this->output = new Stream('php://stdout');
        $this->output->setFormatter(new Line('%message%'));
    }

    /**
     * Provides facade for internal methods.
     *
     * @param  string $name
     * @param  mixed  $arguments
     * @return mixed
     *
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'info':
            case 'alert':
            case 'critical':
            case 'debug':
            case 'error':
            case 'emergency':
            case 'notice':
            case 'warning':
                return call_user_func_array([$this->output, $name], $arguments);
        }

        $bt = debug_backtrace();

        throw new Exception(
            sprintf(
                'Call to undefined method %s:%s at %s:%s.',
                get_class($this), $name, $bt[0]['file'], $bt[0]['line']
            )
        );
    }

}
