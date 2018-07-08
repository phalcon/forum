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

namespace Phosphorum\Core\Events;

use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Mvc\View\Exception;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Events\ViewListener
 *
 * @package Phosphorum\Core\Events
 */
class ViewListener implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Notify about not found views.
     *
     * @param Event             $event
     * @param ViewBaseInterface $view
     * @param mixed             $viewEnginePath
     *
     * @throws Exception
     */
    public function notFoundView(Event $event, ViewBaseInterface $view, $viewEnginePath)
    {
        if (empty($viewEnginePath)) {
            $viewEnginePath = [];
        } elseif (is_array($viewEnginePath) == false) {
            $viewEnginePath = [$viewEnginePath];
        }

        $message = sprintf(
            'View was not found in any of the views directory. Active render paths: [%s]',
            implode(', ', $viewEnginePath)
        );

        $this->safeLogError($this->getDI(), $message);

        if ($event->isCancelable()) {
            $event->stop();
        }

        throw new Exception($message);
    }

    /**
     * Try to log if logger is instantiated.
     *
     * @param  DiInterface $container
     * @param  string      $message
     *
     * @return void
     */
    private function safeLogError(DiInterface $container, string $message): void
    {
        if ($container->has(AdapterInterface::class)) {
            $container->get(AdapterInterface::class)->error($message);
        }
    }
}
