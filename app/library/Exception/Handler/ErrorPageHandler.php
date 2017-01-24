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

use Whoops\Handler\Handler;
use Whoops\Exception\Formatter;

/**
 * Phosphorum\Exception\Handler\ErrorPageHandler
 *
 * @package Phosphorum\Error\Handler
 */
class ErrorPageHandler extends Handler
{
    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function handle()
    {
        $exception = $this->getException();

        if (!$exception instanceof \Exception && !$exception instanceof \Throwable) {
            return Handler::DONE;
        }

        if (!container()->has('view') || !container()->has('dispatcher') || !container()->has('response')) {
            return Handler::DONE;
        }

        switch ($exception->getCode()) {
            case E_WARNING:
            case E_NOTICE:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
            case E_ALL:
                return Handler::DONE;
        }

        $this->renderErrorPage();

        return Handler::QUIT;
    }

    private function renderErrorPage()
    {
        $config     = singleton('config')->error;
        $dispatcher = singleton('dispatcher');
        $view       = singleton('view');
        $response   = singleton('response');

        $error = (object) Formatter::formatExceptionAsDataArray($this->getInspector(), true);

        $dispatcher->setControllerName($config->controller);
        $dispatcher->setActionName($config->action);
        $dispatcher->setParams(['error' => $error]);

        $view->start();
        $dispatcher->dispatch();
        $view->render($config->controller, $config->action, $dispatcher->getParams());
        $view->finish();

        $response->setContent($view->getContent())->send();
    }
}
