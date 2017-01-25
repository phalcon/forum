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

/**
 * Phosphorum\Console\AbstractTask
 *
 * @package Phosphorum\Console
 */
class AbstractTask extends Injectable implements TaskInterface
{
    /**
     * Current output stream.
     * @var Stream
     */
    protected $output;

    /**
     * The base application path.
     * @var string
     */
    protected $basePath;

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
     * Print output to the STDIN.
     *
     * @param string $message
     * @param array  $context
     */
    public function output($message, array $context = null)
    {
        $this->output->info($message, $context);
    }

    /**
     * Determines if a command exists on the current environment.
     *
     * @param  string $command The command to check
     * @return bool
     */
    protected function isShellCommandExist($command)
    {
        $where = sprintf("%s %s", (PHP_OS == 'WINNT') ? 'where' : 'command -v', escapeshellarg($command));

        $process = proc_open(
            $where,
            [
                ['pipe', 'r'], // STDIN
                ['pipe', 'w'], // STDOUT
                ['pipe', 'w'], // STDERR
            ],
            $pipes
        );

        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            if (is_resource($pipes[1])) {
                fclose($pipes[1]);
            }

            if (is_resource($pipes[2])) {
                fclose($pipes[2]);
            }

            if (is_resource($process)) {
                proc_close($process);
            }

            if ($stderr) {
                trigger_error($stderr, E_USER_WARNING);
            }

            return $stdout != '';
        }

        return false;
    }

    /**
     * Run shell command
     *
     * @param  string $cmd
     * @param  bool   $failNonZero
     * @return array
     *
     * @throws Exception
     */
    protected function runShellCommand($cmd, $failNonZero = true)
    {
        $data = [];
        exec(sprintf('%s', escapeshellcmd($cmd)), $data, $resultCode);

        if ($resultCode !== 0 && $failNonZero) {
            throw new Exception("Result code was {$resultCode} for command {$cmd}.");
        }

        return $data;
    }

    /**
     * Setting up concrete task.
     */
    protected function setUp()
    {
        $this->output = new Stream('php://stdout');
        $this->output->setFormatter(new Line('%message%'));

        $this->basePath = dirname(app_path());
    }
}
