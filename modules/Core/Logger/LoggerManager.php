<?php
declare(strict_types=1);

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-present Phalcon Team and contributors               |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Core\Logger;

use Phalcon\Config;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Phalcon\Logger\Formatter\Line;
use Phalcon\Registry;

/**
 * Phosphorum\Core\Logger\LoggerManager
 *
 * @package Phosphorum\Core\Logger
 */
final class LoggerManager
{
    const LOG_LEVEL = 'debug';
    const MESSAGE_FORMAT = '[%date%][%type%] %message%';
    const DATE_FORMAT = 'd-M-Y H:i:s';
    const FILE_NAME = 'application.log';

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
     * Creates the application logger instance.
     *
     * @param Registry $registry
     * @param Config   $config
     *
     * @return File
     */
    public function create(Registry $registry, Config $config): File
    {
        $level = $this->createLogLevel($config);

        $storagePath = $this->createLogsStoragePath($registry, $config);
        $filename = $this->createLogFileName($config);

        $messageFormat = $this->createMessageFormat($config);
        $dateFormat = $this->createDateFormat($config);

        $logger = new File($storagePath . DIRECTORY_SEPARATOR . $filename);

        $logger->setFormatter(new Line($messageFormat, $dateFormat));
        $logger->setLogLevel($level);

        return $logger;
    }

    /**
     * Creates the log level.
     *
     * @param Config $config
     *
     * @return int
     */
    protected function createLogLevel(Config $config): int
    {
        $level = $config->get('level', self::LOG_LEVEL);

        if (!array_key_exists($level, $this->logLevels)) {
            $level = Logger::DEBUG;
        } else {
            $level = $this->logLevels[$level];
        }

        return (int) $level;
    }

    /**
     * Creates the log storage path.
     *
     * @param  Registry $registry
     * @param  Config   $config
     *
     * @return string
     */
    protected function createLogsStoragePath(Registry $registry, Config $config): string
    {
        $path = $config->get('path', $registry->offsetGet('paths')->storage);

        return rtrim($path, '\\/') . DIRECTORY_SEPARATOR . 'logs';
    }

    /**
     * Creates the log file name.
     *
     * @param  Config $config
     *
     * @return string
     */
    protected function createLogFileName(Config $config): string
    {
        $filename = $config->get('filename', self::FILE_NAME);
        $filename = trim($filename);

        if (strpos($filename, '.log') === false) {
            $filename = rtrim($filename, '.') . '.log';
        }

        return (string) $filename;
    }

    /**
     * Creates the log message format.
     *
     * @param  Config $config
     *
     * @return string
     */
    protected function createMessageFormat(Config $config): string
    {
        return (string) $config->get('format', self::MESSAGE_FORMAT);
    }

    /**
     * Creates the log date format.
     *
     * @param  Config $config
     *
     * @return string
     */
    protected function createDateFormat(Config $config): string
    {
        return (string) $config->get('date', self::DATE_FORMAT);
    }
}
