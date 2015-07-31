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
 * @property \Phalcon\Cache\Backend\Memory viewCache
 * @property \Phalcon\Config config
 * @property \Phosphorum\Utils\Slug slug
 */
class ControllerBase extends Controller
{
    public function onConstruct()
    {
        $last_threads = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => 'Phosphorum\Models\Posts'])
            ->groupBy("p.id")
            ->join('Phosphorum\Models\Categories', "r.id = p.categories_id", 'r')
            ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
            ->columns(['p.title as title_post', 'p.id as id_post', 'p.slug as slug_post', 'r.name as name_category', 'u.name as name_user'])
            ->orderBy('p.created_at DESC')
            ->limit(3)
            ->getQuery()
            ->execute();

        $users = Users::find()->getLast();

        $this->view->setVars([
            'threads'      => Posts::count(),
            'last_threads' => $last_threads,
            'users'        => Users::count(),
            'users_latest' => ($users ? $users->login : null),
            'actionName'   => $this->dispatcher->getActionName(),
        ]);
    }
}
