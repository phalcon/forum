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

namespace Phosphorum\Controller;

/**
 * Phosphorum\Controller\Controller
 *
 * @package Phosphorum\Controller
 */
class ErrorController extends ControllerBase
{
    /**
     * @var \stdClass
     */
    protected $error;

    public function initialize()
    {
        if ($this->dispatcher->hasParam('error')) {
            $this->error = $this->dispatcher->getParam('error');
        }

        $this->view->setVars([
            'debug'   => container('config')->application->debug,
            'support' => container('config')->site->support,
        ]);
    }

    public function route400Action()
    {
        $this->createError(
            'Bad request',
            400,
            'Something is not quite right.',
            __LINE__
        );
    }

    public function route401Action()
    {
        $this->response->setHeader('WWW-Authenticate', 'Digest realm="Access denied"');

        $this->createError(
            'Authorization required',
            401,
            'To access the requested resource requires authentication.',
            __LINE__
        );
    }

    public function route403Action()
    {
        $this->createError(
            'Access is denied',
            403,
            'Access to this resource is denied by the administrator.',
            __LINE__
        );
    }

    public function route404Action()
    {
        $this->createError(
            'Page not found',
            404,
            "Sorry! We can't seem to find the page you're looking for.",
            __LINE__
        );
    }

    public function route500Action()
    {
        $this->response->setHeader('Retry-After', 3600);

        $this->createError(
            'Something is not quite right',
            500,
            'We&rsquo;ll be back soon!',
            __LINE__
        );
    }

    public function route503Action()
    {
        $this->createError(
            'Site Maintenance',
            503,
            'Unfortunately an unexpected system error occurred.',
            __LINE__
        );
    }

    protected function createError($title, $code, $message, $line)
    {
        $error = $this->error;

        if (!is_object($error)) {
            $error = (object) [
                'type'    => -1,
                'message' => $title,
                'file'    => __FILE__,
                'line'    => $line,
                'trace'   => [],
            ];
        }

        $this->tag->setTitle($title);
        $this->response->setStatusCode($code);

        $this->view->setVars([
            'code'    => $code,
            'error'   => $error,
            'message' => $message,
        ]);
    }
}
