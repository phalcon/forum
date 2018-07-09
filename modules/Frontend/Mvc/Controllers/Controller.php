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

use Phalcon\Assets\Filters\Cssmin;
use Phalcon\Assets\Filters\Jsmin;
use Phalcon\Platform\Mvc\Controller as ControllerBase;
use Phosphorum\Core\Environment;
use Phosphorum\Core\Paginator\PaginatorManager;
use Phosphorum\Domain\Factories\CategoryFactory;
use Phosphorum\Domain\Factories\PostFactory;
use Phosphorum\Domain\Factories\PostTrackingFactory;
use Phosphorum\Domain\Services\CategoryService;
use Phosphorum\Domain\Services\PostService;
use Phosphorum\Domain\Services\PostTrackingService;

/**
 * Phosphorum\Frontend\Mvc\Controllers\Controller
 *
 * @property \Phalcon\Assets\Manager $assets
 * @property \Phalcon\Http\Request|\Phalcon\Http\RequestInterface $request
 * @property \Phalcon\Mvc\Dispatcher|\Phalcon\Mvc\DispatcherInterface $dispatcher
 * @property \Phalcon\Mvc\Url|\Phalcon\Mvc\UrlInterface $url
 * @property \Phalcon\Mvc\View $view
 * @property \Phalcon\Session\Adapter $session
 * @property \Phalcon\Tag $tag
 * @property \Phosphorum\Core\Modules\ModuleInterface $module
 *
 * @package Phosphorum\Frontend\Mvc\Controllers
 */
class Controller extends ControllerBase
{
    /** @var PostTrackingService */
    protected $postTrackingService;

    /** @var PostService */
    protected $postService;

    /** @var CategoryService */
    protected $categoryService;

    /** @var PaginatorManager */
    protected $paginatorManager;

    /** @var null|int */
    protected $loggedUserId = null;

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
     * {@inheritdoc}
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setupSessionVariables();
        $this->setupServices();
        $this->setupGlobalTemplateVars();
    }

    /**
     * Utileze session variables.
     *
     * @return void
     */
    protected function setupSessionVariables(): void
    {
        if ($this->session->isStarted() == false) {
            return;
        }

        if ($this->session->has('identity')) {
            $this->loggedUserId = (int) $this->session->get('identity');
        }

        if ($this->session->has('identity-timezone')) {
            date_default_timezone_set($this->session->get('identity-timezone'));
        }
    }

    /**
     * Setting up global services.
     *
     * @return void
     */
    protected function setupServices(): void
    {
        $this->postTrackingService = $this->getDI()
            ->get(PostTrackingFactory::class)
            ->createService();

        $this->postService = $this->getDI()
            ->get(PostFactory::class)
            ->createService();

        $this->categoryService = $this->getDI()
            ->get(CategoryFactory::class)
            ->createService();

        $this->paginatorManager = $this->getDI()->get(PaginatorManager::class);
    }

    /**
     * Setting up View's global variables.
     *
     * @return void
     */
    protected function setupGlobalTemplateVars(): void
    {
        $this->view->setVars([
            'action_name' => $this->dispatcher->getActionName(),
            'controller_name' => $this->dispatcher->getControllerName(),
            'threads_count' => $this->postService->countAll(),
        ]);
    }
}
