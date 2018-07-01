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

namespace Phosphorum\Core\Mvc;

use Phalcon\Di\Injectable;
use Phalcon\DiInterface;
use Phalcon\Mvc\ControllerInterface;
use Phalcon\Platform\Traits\InjectionAwareTrait;

/**
 * Phosphorum\Core\Mvc\Controller
 *
 * @property \Phalcon\Tag $tag
 * @property \Phalcon\Mvc\View $view
 * @property \Phalcon\Mvc\Url|\Phalcon\Mvc\UrlInterface $url
 * @property \Phalcon\Assets\Manager $assets
 * @property \Phosphorum\Core\Modules\ModuleInterface $module
 * @property \Phalcon\Mvc\Dispatcher|\Phalcon\Mvc\DispatcherInterface $dispatcher
 *
 * @method void onConstruct()
 *
 * @package Phosphorum\Core\Mvc
 */
abstract class Controller extends Injectable implements ControllerInterface
{
    use InjectionAwareTrait {
        InjectionAwareTrait::__construct as protected __DiInject;
    }

    /**
     * Controller constructor.
     *
     * @param DiInterface|null $container
     */
    final public function __construct(DiInterface $container = null)
    {
        $this->__DiInject($container);

        $this->setEventsManager($this->getDI()->getShared('eventsManager'));
        $this->setViewGlobalVariables();

        if (method_exists($this, 'onConstruct')) {
            $this->onConstruct();
        }
    }

    /**
     * Set View's global variables.
     *
     * @return void
     */
    private function setViewGlobalVariables(): void
    {
        $this->view->setVars([
            'action_name'     => $this->dispatcher->getActionName(),
            'controller_name' => $this->dispatcher->getControllerName(),
        ]);
    }
}
