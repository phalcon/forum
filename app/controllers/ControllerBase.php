<?php

namespace Phosphorum\Controllers;

use Phalcon\Mvc\Controller;
use Phosphorum\Models\Posts;
use Phosphorum\Models\Users;

/**
 * Class ControllerBase
 *
 * @package Phosphorum\Controllers
 *
 * @property \Phalcon\Flash\Session flashSession
 * @property \Phalcon\Escaper escaper
 * @property \Phalcon\Session\Adapter\Files session
 * @property \Phalcon\Tag tag
 * @property \Phalcon\Mvc\Model\Manager modelsManager
 * @property \Phalcon\Db\Adapter\Pdo\Mysql db
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Flash\Direct flash
 * @property \Phalcon\Cache\Backend\Memory viewCache
 * @property \Phalcon\Security security
 * @property \Phalcon\Config config
 * @property \Phalcon\Mvc\Router router
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 */
class ControllerBase extends Controller
{

    public function onConstruct()
    {
        $last_threads = $this
            ->modelsManager
            ->createBuilder()
            ->from(array('p' => 'Phosphorum\Models\Posts'))
            ->groupBy("p.id")
            ->join('Phosphorum\Models\Categories', "r.id = p.categories_id", 'r')
            ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
            ->columns(array('p.title as title_post', 'p.id as id_post', 'p.slug as slug_post', 'r.name as name_category', 'u.name as name_user'))
            ->orderBy('p.created_at DESC')
            ->limit(3)
            ->getQuery()
            ->execute();

        $users = Users::find()->getLast();

        $this->view->setVars([
            'threads'      => Posts::count(),
            'last_threads' => $last_threads,
            'users'        => Users::count(),
            'users_latest' => $users->login,
            'actionName'   => $this->dispatcher->getActionName(),
        ]);
    }
}
