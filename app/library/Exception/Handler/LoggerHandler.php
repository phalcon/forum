<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2017 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Exception\Handler;

use Whoops\Exception\Frame;
use Whoops\Handler\Handler;

/**
 * Phosphorum\Exception\Handler\LoggerHandler
 *
 * @package Phosphorum\Error\Handler
 */
class LoggerHandler extends Handler
{
    const VAR_DUMP_PREFIX = '   | ';

    /**
     * @var bool
     */
    private $addTraceToOutput = true;

    /**
     * @var bool|integer
     */
    private $addTraceFunctionArgsToOutput = false;

    /**
     * @var integer
     */
    private $traceFunctionArgsOutputLimit = 1024;

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function handle()
    {
        $response = $this->generateResponse();

        if ($logger = $this->getLogger()) {
            $logger->error($response);
        }

        return Handler::DONE;
    }

    /**
     * Create plain text response and return it as a string.
     *
     * @return string
     */
    public function generateResponse()
    {
        $exception = $this->getException();

        return sprintf(
            "%s: %s in file %s on line %d%s\n",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $this->getTraceOutput()
        );
    }

    /**
     * Add error trace function arguments to output.
     * Set to True for all frame args, or integer for the n first frame args.
     *
     * @param  bool|integer|null $addTraceFunctionArgsToOutput
     * @return null|bool|integer
     */
    public function addTraceFunctionArgsToOutput($addTraceFunctionArgsToOutput = null)
    {
        if (func_num_args() == 0) {
            return $this->addTraceFunctionArgsToOutput;
        }

        if (!is_integer($addTraceFunctionArgsToOutput)) {
            $this->addTraceFunctionArgsToOutput = (bool) $addTraceFunctionArgsToOutput;
        } else {
            $this->addTraceFunctionArgsToOutput = $addTraceFunctionArgsToOutput;
        }
    }

    /**
     * Get the size limit in bytes of frame arguments var_dump output.
     * If the limit is reached, the var_dump output is discarded.
     * Prevent memory limit errors.
     *
     * @return integer
     */
    public function getTraceFunctionArgsOutputLimit()
    {
        return $this->traceFunctionArgsOutputLimit;
    }

    /**
     * Add error trace to output.
     *
     * @param  bool|null $addTraceToOutput
     * @return bool|$this
     */
    public function addTraceToOutput($addTraceToOutput = null)
    {
        if (func_num_args() == 0) {
            return $this->addTraceToOutput;
        }

        $this->addTraceToOutput = (bool) $addTraceToOutput;
        return $this;
    }

    /**
     * @return string
     */
    public function contentType()
    {
        return 'text/plain';
    }

    private function getLogger()
    {
        if (container()->has('logger')) {
            return container('logger');
        }

        return null;
    }

    /**
     * Get the exception trace as plain text.
     *
     * @return string
     */
    private function getTraceOutput()
    {
        if (!$this->addTraceToOutput()) {
            return '';
        }
        $inspector = $this->getInspector();
        $frames = $inspector->getFrames();

        $response = "\nStack trace:";

        $line = 1;
        foreach ($frames as $frame) {
            /** @var Frame $frame */
            $class = $frame->getClass();

            $template = "\n%3d. %s->%s() %s:%d%s";
            if (!$class) {
                // Remove method arrow (->) from output.
                $template = "\n%3d. %s%s() %s:%d%s";
            }

            $response .= sprintf(
                $template,
                $line,
                $class,
                $frame->getFunction(),
                $frame->getFile(),
                $frame->getLine(),
                $this->getFrameArgsOutput($frame, $line)
            );

            $line++;
        }

        return $response;
    }

    /**
     * Get the frame args var_dump.
     *
     * @param  Frame   $frame
     * @param  integer $line
     * @return string
     */
    private function getFrameArgsOutput(Frame $frame, $line)
    {
        if ($this->addTraceFunctionArgsToOutput() === false
            || $this->addTraceFunctionArgsToOutput() < $line
        ) {
            return '';
        }

        // Dump the arguments:
        ob_start();
        var_dump($frame->getArgs());
        if (ob_get_length() > $this->getTraceFunctionArgsOutputLimit()) {
            // The argument var_dump is to big.
            // Discarded to limit memory usage.
            ob_clean();
            return sprintf(
                "\n%sArguments dump length greater than %d Bytes. Discarded.",
                self::VAR_DUMP_PREFIX,
                $this->getTraceFunctionArgsOutputLimit()
            );
        }

        return sprintf(
            "\n%s",
            preg_replace('/^/m', self::VAR_DUMP_PREFIX, ob_get_clean())
        );
    }
}
