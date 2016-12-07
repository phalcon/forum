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
use Phalcon\Cli\Dispatcher;
use Phosphorum\Console\Application;
use Phosphorum\Console\OptionParser;

/**
 * Phosphorum\Listener\CliInputListener
 *
 * @package Phosphorum\Listener
 */
class CliInputListener
{
    /**
     * Parse input options.
     *
     * @param Event       $event
     * @param Application $application
     * @param Dispatcher  $dispatcher
     */
    public function beforeHandleTask(Event $event, Application $application, Dispatcher $dispatcher)
    {
        $parsedOptions = OptionParser::parse($application->getArguments());
        $dispatcher->setParams($this->setUpTaskDefinition($parsedOptions));

        container()->get('logger')->debug(
            sprintf('[%s] Parsed options: %s', $event->getType(), json_encode($parsedOptions))
        );

        container()->get('logger')->debug(
            sprintf('[%s] Dispatcher params: %s', $event->getType(), json_encode($dispatcher->getParams()))
        );
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
     * // 'activeTask'   => cache
     * // 'activeAction' => clear
     * // 'args'         => []
     *
     * $_SERVER['argv'] = [
     *     "./forum",
     *     "cache"
     *     "--clear"
     * ];
     *
     * $inputListener->setUpTaskDefinition(OptionParser::parse($_SERVER['argv']));
     * // 'activeTask'   => cache
     * // 'activeAction' => false
     * // 'args'         => ['clear' => true]
     * </code>
     *
     * @param  array $parsedOptions
     *
     * @return array
     */
    protected function setUpTaskDefinition(array $parsedOptions)
    {
        $activeTask = false;
        $activeAction = false;
        $args = [];

        if (isset($parsedOptions[0])) {
            $activeTask = $parsedOptions[0];
        }

        if (isset($parsedOptions[1])) {
            $activeAction = $parsedOptions[1];
        } elseif (strpos($activeTask, ':')) {
            list($activeTask, $activeAction) = explode(':', $activeTask, 2);
        }

        if (count($parsedOptions) > 2) {
            $args = array_slice($parsedOptions, 2);
        } elseif (count($parsedOptions) > 1 && $activeAction === false) {
            $args = array_slice($parsedOptions, 1);
        }

        return compact('activeTask', 'activeAction', 'args');
    }
}
