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

namespace Phosphorum\Frontend\Mvc\Controllers;

use Phosphorum\Core\Environment;
use Phosphorum\Core\Mvc\Controller as ControllerBase;
use Phalcon\Assets\Filters\Cssmin;
use Phalcon\Assets\Filters\Jsmin;

/**
 * Phosphorum\Frontend\Mvc\Controllers\Controller
 *
 * @property \Phalcon\Breadcrumbs $breadcrumbs
 * @property \Phalcon\Session\Adapter $session
 *
 * @package Phosphorum\Frontend\Mvc\Controllers
 */
class Controller extends ControllerBase
{
    const POSTS_PER_PAGE = 40;

    public function onConstruct(): void
    {
        /** @var Environment $env */
        $env = $this->getDI()->get(Environment::class);

        $this->registerCss($env);
        $this->registerJs($env);
    }

    /**
     * Register default site styles.
     *
     * @param Environment $env
     */
    protected function registerCss(Environment $env): void
    {
        // TODO: Do not minify on CentOS
        // TODO: Add diff.css support
        $this->assets
            ->collection('default_css')
            ->setTargetPath($env->getPath('public/css/style.css'))
            ->setTargetUri('css/style.css')
            ->addCss($this->module->getPath('resources/assets/css/bootstrap.css'), true)
            ->addCss($this->module->getPath('resources/assets/css/material-design-iconic-font.min.css'), true, false)
            ->addCss($this->module->getPath('resources/assets/css/font-awesome.min.css'), true, false)
            ->addCss($this->module->getPath('resources/assets/css/style.css'), true)
            ->addCss($this->module->getPath('resources/assets/css/responsive.css'), true)
            ->addCss('https://fonts.googleapis.com/css?family=Titillium+Web:200,400,600', false, false)
            ->join(true)
            ->addFilter(new Cssmin());
    }

    protected function registerJs(Environment $env): void
    {
        // TODO: Do not minify on CentOS
        // TODO: Add prism.js support
        $this->assets
            ->collection('default_js')
            ->setTargetPath($env->getPath('public/js/scripts.js'))
            ->setTargetUri('js/scripts.js')
            ->addJs($this->module->getPath('resources/assets/js/jquery.min.js'), true, false)
            ->addJs($this->module->getPath('resources/assets/js/popper.min.js'), true, false)
            ->addJs($this->module->getPath('resources/assets/js/bootstrap.min.js'), true, false)
            ->addJs($this->module->getPath('resources/assets/js/scripts.js'), true)
            ->join(true)
            ->addFilter(new Jsmin());
    }

    /**
     * This method is executed first, before any action is executed on a controller.
     *
     * NOTE: The this method is only called if the 'beforeExecuteRoute' event is executed with success.
     *
     * @return void
     */
    public function initialize(): void
    {
        if ($timezone = $this->session->get('identity-timezone')) {
            date_default_timezone_set($timezone);
        }

        $this->breadcrumbs->add('Home', '/');
        $this->view->setVar('limitPost', self::POSTS_PER_PAGE);
    }
}
