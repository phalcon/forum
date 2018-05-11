<?php

namespace Phosphorum\Controller;

use Phalcon\Mvc\Controller;
use Phosphorum\Model\Posts;
use Phosphorum\Model\Users;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Assets\Filters\Jsmin;
use Phalcon\Assets\Filters\Cssmin;

/**
 * Class ControllerBase
 *
 * @package Phosphorum\Controller
 *
 * @property \Phalcon\Cache\BackendInterface viewCache
 * @property \Phalcon\Config config
 * @property \Phosphorum\Provider\UrlResolver\Slug slug
 * @property \Phalcon\Avatar\Avatarable gravatar
 * @property \Phalcon\Logger\AdapterInterface logger
 * @property \Phalcon\Breadcrumbs breadcrumbs
 * @property \Phosphorum\Provider\Security\Security $security
 * @property \Phosphorum\Provider\Captcha\Adapters\ReCaptcha $recaptcha
 */
class ControllerBase extends Controller
{
    const POSTS_IN_PAGE = 40;

    public function onConstruct()
    {
        /**
         * @var \Phalcon\Registry $registry
         */
        $registry = $this->getDI()->get('registry');

        $lastThreads = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Model\Posts'])
            ->groupBy("p.id")
            ->join('Phosphorum\Model\Categories', "r.id = p.categories_id", 'r')
            ->join('Phosphorum\Model\Users', "u.id = p.users_id", 'u')
            ->columns([
                'p.title as title_post',
                'p.id as id_post',
                'p.slug as slug_post',
                'r.name as name_category',
                'u.name as name_user'
            ])
            ->where('p.deleted = 0')
            ->orderBy('p.created_at DESC')
            ->limit(3)
            ->getQuery()
            ->execute();

        /** @var Simple $lastMember */
        $lastMember = Users::find(['order' => 'created_at DESC', 'limit' => 1, 'columns' => 'login']);

        $login = null;
        if ($lastMember->valid()) {
            $login = $lastMember->getFirst()->login;
        }

        $this->view->setVars([
            'threads'        => Posts::count(),
            'last_threads'   => $lastThreads,
            'users'          => Users::count(),
            'users_latest'   => $login,
            'actionName'     => $this->dispatcher->getActionName(),
            'controllerName' => $this->dispatcher->getControllerName(),
        ]);

        $this->assets
            ->collection('globalJs')
            ->setTargetPath($registry->offsetGet('public_path') . 'assets/globalJs.js')
            ->setTargetUri('assets/globalJs.js')
            ->addJs($registry->offsetGet('public_path') . 'js/jquery-3.2.1.min.js', true, false)
            ->addJs($registry->offsetGet('public_path') . 'js/bootstrap.min.js', true, false)
            ->addJs($registry->offsetGet('public_path') . 'js/editor.min.js', true, false)
            ->addJs($registry->offsetGet('public_path') . 'js/forum.js', true)
            ->addJs($registry->offsetGet('public_path') . 'js/prism.js', true)
            ->join(true)
            ->addFilter(new Jsmin);

        if ($this->session->get('identity-theme') == 'L') {
            $this->assets
                ->collection('globalWhiteCss')
                ->setTargetPath($registry->offsetGet('public_path') . 'assets/globalWhiteCss.css')
                ->setTargetUri('assets/globalWhiteCss.css')
                ->addCss($registry->offsetGet('public_path') . 'css/bootstrap.min.css', true, false)
                ->addCss($registry->offsetGet('public_path') . 'css/editor.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/fonts.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/octicons.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/diff.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/style.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/prism.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/theme-white.css', true)
                ->join(true)
                ->addFilter(new Cssmin);
        } else {
            $this->assets
                ->collection('globalCss')
                ->setTargetPath($registry->offsetGet('public_path') . 'assets/globalCss.css')
                ->setTargetUri('assets/globalCss.css')
                ->addCss($registry->offsetGet('public_path') . 'css/bootstrap.min.css', true, false)
                ->addCss($registry->offsetGet('public_path') . 'css/editor.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/fonts.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/octicons.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/diff.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/style.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/prism.css', true)
                ->addCss($registry->offsetGet('public_path') . 'css/theme.css', true)
                ->join(true)
                ->addFilter(new Cssmin);
        }
    }

    /**
     * This initializes the timezone in each request
     */
    public function initialize()
    {
        if ($timezone = $this->session->get('identity-timezone')) {
            date_default_timezone_set($timezone);
        }

        $this->breadcrumbs->add('Home', '/');
        $this->view->setVar('limitPost', self::POSTS_IN_PAGE);
    }

    /**
     * This method prepares the queries to be executed in each list of posts
     * The returned builders are used as base in the search, tagged list and index lists
     *
     * @param bool $joinReply
     * @return array
     */
    protected function prepareQueries($joinReply = false)
    {
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        $itemBuilder = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Model\Posts'])
            ->orderBy('p.sticked DESC, p.created_at DESC');

        if ($joinReply) {
            $itemBuilder
                ->groupBy('p.id')
                ->join('Phosphorum\Model\PostsReplies', 'r.posts_id = p.id', 'r');
        }

        $totalBuilder = clone $itemBuilder;

        $itemBuilder
            ->columns(['p.*'])
            ->limit(self::POSTS_IN_PAGE);

        $totalBuilder
            ->columns('COUNT(*) AS count');

        return [$itemBuilder, $totalBuilder];
    }
    /**
     * Validation Google captcha
     *
     * @return boolean
     */
    protected function checkCaptcha()
    {
        if (!$this->recaptcha->isEnabled()) {
            return true;
        }

        if ($this->isUserTrust()) {
            return true;
        }

        if (!$this->request->hasPost('g-recaptcha-response')) {
            $this->flashSession->error('Please confirm that you are not a bot.');
            return false;
        }

        $captcha = $this->recaptcha->getCaptcha();
        $resp = $captcha->verify($this->request->getPost('g-recaptcha-response'), $this->request->getClientAddress());

        if (!$resp->isSuccess()) {
            $this->flashSession->error('Please confirm that you are not a bot.');
            return false;
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    protected function isUserTrust()
    {
        $karma = $this->session->get('identity-karma');

        if (isset($karma) && $karma > 300) {
            return true;
        }

        return false;
    }
}
