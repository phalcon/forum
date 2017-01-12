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

namespace Phosphorum\Listener;

use Phalcon\Events\Event;
use Phosphorum\Console\Application;
use Phosphorum\Console\OptionParser;

/**
 * Phosphorum\Listener\CliInputListener
 *
 * @package Phosphorum\Listener
 */
class CliInputListener
{
    const SEPARATOR = ':';

    /**
     * Parse and prepare the task definition from the input options.
     *
     * @param  Event       $event
     * @param  Application $application
     * @return bool
     */
    public function boot(Event $event, Application $application)
    {
        $parsedOptions = OptionParser::parse($application->getRawArguments());
        $definition    = $this->setUpTaskDefinition($parsedOptions);

        $application->setArguments($definition);

        container()->get('logger')->debug(
            sprintf('[%s] Parsed options: %s', $event->getType(), json_encode($parsedOptions))
        );

        container()->get('logger')->debug(
            sprintf('[%s] Task definition: %s', $event->getType(), json_encode($definition))
        );

        return true;
    }

    /**
     * Setting up the task definition.
     *
     * <code>
     * $_SERVER['argv'] = [
     *     "./forum",
     *     "cache:clear"
     * ];
     *
     * $inputListener->setUpTaskDefinition(OptionParser::parse($_SERVER['argv']));
     * // 'task'   => cache
     * // 'action' => clear
     * // 'params' => []
     *
     * $_SERVER['argv'] = [
     *     "./forum",
     *     "cache"
     *     "--clear"
     * ];
     *
     * $inputListener->setUpTaskDefinition(OptionParser::parse($_SERVER['argv']));
     * // 'task'   => cache
     * // 'action' => false
     * // 'params' => ['clear' => true]
     * </code>
     *
     * @param  array $parsedOptions
     * @return array
     */
    protected function setUpTaskDefinition(array $parsedOptions)
    {
        $task   = null;
        $action = null;
        $params = [];

        if (isset($parsedOptions[0])) {
            $task = $parsedOptions[0];
        }

        if (isset($parsedOptions[1])) {
            $action = $parsedOptions[1];
        } elseif (strpos($task, self::SEPARATOR)) {
            list($task, $action) = explode(self::SEPARATOR, $task, 2);
        }

        if (count($parsedOptions) > 2) {
            $params = array_slice($parsedOptions, 2);
        } elseif (count($parsedOptions) > 1) {
            $params = array_slice($parsedOptions, 1);
        }

        if ($task == null) {
            $task = $this->findCommandFromOptions($parsedOptions);
        }

        return compact('task', 'action', 'params');
    }

    protected function findCommandFromOptions(array $options)
    {
        if (empty($options)) {
            return null;
        }

        switch (key($options)) {
            case 'H':
            case 'help':
                return 'help';
            case 'version':
            case 'V':
                return 'version';
            case 'list':
                return 'commands';
        }

        return null;
    }
}
