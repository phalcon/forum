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

namespace Phosphorum\Provider\Logger;

use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Phalcon\Logger\Formatter\Line;
use Phosphorum\Provider\AbstractServiceProvider;

/**
 * Phosphorum\Provider\Logger\ServiceProvider
 *
 * @package Phosphorum\Provider\Logger
 */
class ServiceProvider extends AbstractServiceProvider
{
    const DEFAULT_LEVEL = 'debug';
    const DEFAULT_FORMAT = '[%date%][%type%] %message%';
    const DEFAULT_DATE = 'd-M-Y H:i:s';

    /**
     * The Service name.
     * @var string
     */
    protected $serviceName = 'logger';

    protected $logLevels = [
        'emergency' => Logger::EMERGENCY,
        'emergence' => Logger::EMERGENCE,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'error'     => Logger::ERROR,
        'warning'   => Logger::WARNING,
        'notice'    => Logger::NOTICE,
        'info'      => Logger::INFO,
        'debug'     => Logger::DEBUG,
        'custom'    => Logger::CUSTOM,
        'special'   => Logger::SPECIAL,
    ];

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register()
    {
        $logLevels = $this->logLevels;

        $this->di->set(
            $this->serviceName,
            function ($filename = null, $format = null) use ($logLevels) {
                $config = container('config')->logger;

                // Setting up the log level
                if (empty($config->level)) {
                    $level = self::DEFAULT_LEVEL;
                } else {
                    $level = strtolower($config->level);
                }

                if (!array_key_exists($level, $logLevels)) {
                    $level = Logger::DEBUG;
                } else {
                    $level = $logLevels[$level];
                }

                // Setting up date format
                if (empty($config->date)) {
                    $date = self::DEFAULT_DATE;
                } else {
                    $date = $config->date;
                }

                // Format setting up
                if (empty($format)) {
                    if (!isset($config->format)) {
                        $format = self::DEFAULT_FORMAT;
                    } else {
                        $format = $config->format;
                    }
                }

                // Setting up the filename
                $filename = trim($filename ?: $config->filename, '\\/');

                if (!strpos($filename, '.log')) {
                    $filename = rtrim($filename, '.') . '.log';
                }

                $logger = new File(rtrim($config->path, '\\/') . DIRECTORY_SEPARATOR . $filename);

                $logger->setFormatter(new Line($format, $date));
                $logger->setLogLevel($level);

                return $logger;
            }
        );
    }
}
